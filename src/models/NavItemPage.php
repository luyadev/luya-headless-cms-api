<?php

namespace luya\headless\cms\api\models;

use luya\cms\base\BlockInterface;
use luya\helpers\ArrayHelper;
use luya\helpers\Inflector;
use ReflectionClass;
use yii\db\ActiveRecord;

class NavItemPage extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_nav_item_page';
    }

    public function getNavItem()
    {
        return $this->hasOne(NavItem::class, ['id' => 'nav_item_id']);
    }

    public function getLayout()
    {
        return $this->hasOne(Layout::class, ['id' => 'layout_id']);
    }

    public function getBlocks()
    {
        return $this->hasMany(PageBlock::class, ['nav_item_page_id' => 'id'])->andWhere(['is_hidden' => false]);
    }

    public function extraFields()
    {
        return array_merge(parent::extraFields(), ['blocks', 'navItem', 'layout']);
    }

    public function getContent()
    {
        $result = [];
        foreach ($this->layout->getPlaholdersList() as $placeholderName) {

            $placholders = $this->buildTree($this->blocks, 0, $placeholderName);
            
            $result[$placeholderName] = array_key_exists($placeholderName, $placholders) ? $placholders[$placeholderName] : [];
        }

        return $result;
    }

    private function buildTree(array $blocks, $prevId, $placeholderName)
    {
        $result = [];
        foreach ($blocks as $block) {
            if ($block->prev_id == $prevId) {
                $placeholders = $this->buildTree($blocks, $block->id, $block->placeholder_var);

                /** @var BlockInterface $object */
                $object = $block->block->getClassObject();
                $object->setVarValues($block->getEnsuredValues());
                $object->setCfgValues($block->getEnsuredConfigs());

                $reflect = new ReflectionClass($object);

                $newItem = [
                    'id' => $block->id,
                    'index' => $block->sort_index,
                    'block_id' => $block->block_id,
                    'block_name' => $reflect->getShortName(),
                    'full_block_name' => Inflector::camelize($block->block->class),
                    'is_container' => $object->getIsContainer(),
                    'values' => $block->getEnsuredValues(),
                    'cfgs' => $block->getEnsuredConfigs(),
                    'extras' => $object->getExtraVarValues(),
                ];
                
                if ($object->getIsContainer()) {
                    // ensure all placeholders exists
                    foreach ($object->getConfigPlaceholdersExport() as $placeholderName) {
                        $newItem['placeholders'][$placeholderName['var']] = array_key_exists($placeholderName['var'], $placeholders) ? $placeholders[$placeholderName['var']] : [];
                    }
                }

                $result[$block->placeholder_var][] = $newItem;
            }
        }

        ArrayHelper::multisort($result, 'index', SORT_ASC);

        return $result;
    }
}