<?php

namespace luya\headless\cms\api\models;

use luya\helpers\ArrayHelper;
use yii\db\ActiveRecord;

/**
 * Class Container
 * @package luya\headless\cms\api\models
 *
 * @property int $id
 * @property int $website_id
 * @property string $name
 * @property string $alias
 * @property bool $is_deleted
 *
 * @property Nav[] $navs
 * @property NavItem[] $items
 */
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
        return $this->hasMany(Nav::class, ['nav_container_id' => 'id'])
            ->andOnCondition(['is_offline' => false, 'is_draft' => false, 'cms_nav.is_deleted' => false])
            ->orderBy(['sort_index' => SORT_ASC]);
    }

    public function getItems()
    {
        return $this->hasMany(NavItem::class, ['nav_id' => 'id'])->via('navs');
    }

    public function containerToMenu(Container $container)
    {
        return $this->buildTree($container->items, 0, '');
    }

    function buildTree(array $items, $parentId, $pathPrefix)
    {
        $result = [];
        /** @var NavItem $item */
        foreach ($items as $item) {
            if ($item->nav->parent_nav_id == $parentId) {
                $currentPath = empty($pathPrefix) ? $item->alias : "{$pathPrefix}/{$item->alias}";
                $newItem = [
                    'id' => $item->id,
                    'index' => $item->nav->sort_index,
                    'nav_id' => $item->nav_id,
                    'lang_id' => $item->lang_id,
                    'is_hidden' => $item->nav->is_hidden,
                    'is_home' => $item->nav->is_home,
                    'title' => $item->title,
                    'title_tag' => $item->title_tag,
                    'alias' => $item->alias,
                    'path' => $currentPath,
                    'description' => $item->description,
                    'children' => $this->buildTree($items, $item->id, $currentPath),
                ];

                $newItem['has_children'] = count($newItem['children']) > 0;
                $result[] = $newItem;
            }
        }

        ArrayHelper::multisort($result, 'index', SORT_ASC);

        return $result;
    }
}
