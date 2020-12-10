<?php

namespace luya\headless\cms\models;

use luya\helpers\Inflector;
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
                $newItem = [
                    'id' => $block->id,
                    'block_id' => $block->block_id,
                    'block_name' => Inflector::camelize($block->block->class),
                    'values' => $block->json_config_values,
                    'cfgs' => $block->json_config_cfg_values,
                ];

                if (count($placeholders) > 0) {
                    $newItem['placeholders'] = $placeholders;
                }

                $result[$block->placeholder_var][] = $newItem;
            }
        }

        return $result;
    }
}