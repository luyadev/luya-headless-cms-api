<?php

namespace luya\headless\cms\api\models;

use luya\cms\base\BlockInterface;
use luya\helpers\ArrayHelper;
use luya\helpers\Inflector;
use ReflectionClass;
use yii\db\ActiveRecord;

/**
 * Class NavItemPage
 * @package luya\headless\cms\api\models
 *
 * @property int $id
 * @property int $layout_id
 * @property int $nav_item_id
 * @property int $timestamp_create
 * @property int $create_user_id
 * @property string $version_alias
 * @property int $timestamp_update
 *
 * @property NavItem $navItem
 * @property Layout $layout
 * @property PageBlock[] $blocks
 */
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
        foreach ($this->layout->getPlaceholdersList() as $placeholderName) {

            $placholders = $this->buildTree($this->blocks, 0, $placeholderName);

            $result[$placeholderName] = array_key_exists($placeholderName, $placholders) ? $placholders[$placeholderName] : [];
        }

        return $result;
    }

    /**
     * @param PageBlock[] $blocks
     * @param $prevId
     * @param $placeholderName
     * @return array
     * @throws \ReflectionException
     */
    private function buildTree(array $blocks, $prevId, $placeholderName)
    {
        $result = [];
        foreach ($blocks as $block) {
            /** @var PageBlock $block */
            if ($block->prev_id == $prevId) {
                $placeholders = $this->buildTree($blocks, $block->id, $block->placeholder_var);

                /** @var BlockInterface $object */
                $object = $block->block->getClassObject($block->id, 'frontend');
                $object->setVarValues($block->getEnsuredValues());
                $object->setCfgValues($block->getEnsuredConfigs());

                $reflect = new ReflectionClass($object);

                if ($object->getIsContainer()) {
                    // ensure all placeholders exists
                    $insertedHolders = [];
                    foreach ($object->getConfigPlaceholdersExport() as $placeholderName) {
                        $insertedHolders[$placeholderName['var']] = array_key_exists($placeholderName['var'], $placeholders) ? $placeholders[$placeholderName['var']] : [];
                    }
                    $object->setPlaceholderValues($insertedHolders);
                }

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
                    $newItem['placeholders'] = $insertedHolders;
                }

                $result[$block->placeholder_var][] = $newItem;
            }
        }

        foreach ($result as $placeholderVar => $items) {
            ArrayHelper::multisort($result[$placeholderVar], 'index', SORT_ASC);
        }

        return $result;
    }
}
