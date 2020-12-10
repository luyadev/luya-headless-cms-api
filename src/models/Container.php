<?php

namespace luya\headless\cms\models;

use yii\db\ActiveRecord;

class Container extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_nav_container';
    }

    public function extraFields()
    {
        return array_merge(parent::extraFields(), ['items']);
    }

    public function getNavs()
    {
        return $this->hasMany(Nav::class, ['nav_container_id' => 'id'])->orderBy(['sort_index' => SORT_ASC]);
    }

    public function getItems()
    {
        return $this->hasMany(NavItem::class, ['nav_id' => 'id'])->via('navs');
    }

    public function containerToMenu(Container $container)
    {
        return $this->buildTree($container->items, 0);
    }

    function buildTree(array $items, $parentId)
    {
        $result = [];
        foreach ($items as $item) {
            if ($item->nav->parent_nav_id == $parentId) {
                $newItem = [
                    'id' => $item->id,
                    'nav_id' => $item->nav_id,
                    'is_home' => $item->nav->is_home,
                    'title' => $item->title,
                    'slug' => $item->alias,
                    'description' => $item->description,
                    'children' => $this->buildTree($items, $item->id),
                ];

                $newItem['has_children'] = count($newItem['children']) > 0 ? true : false;
                $result[] = $newItem;
            }
        }
      
        return $result;
    }
}