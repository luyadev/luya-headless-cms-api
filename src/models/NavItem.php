<?php

namespace luya\headless\cms\api\models;

use yii\db\ActiveRecord;

/**
 * Class NavItem
 * @package luya\headless\cms\api\models
 *
 * @property int $id
 * @property int $nav_id
 * @property int $lang_id
 * @property int $nav_item_type
 * @property int $nav_item_type_id
 * @property int $create_user_id
 * @property int $update_user_id
 * @property int $timestamp_create
 * @property int $timestamp_update
 * @property string $title
 * @property string $alias
 * @property string $description
 * @property string $keywords
 * @property string $title_tag
 * @property int $image_id
 * @property bool $is_url_strict_parsing_disabled
 * @property bool $is_cacheable
 *
 * @property Nav $nav
 * @property NavItemPage $currentPage
 */
class NavItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_nav_item';
    }

    public function extraFields()
    {
        return array_merge(parent::extraFields(), ['currentPage']);
    }

    public function getNav()
    {
        return $this->hasOne(Nav::class, ['id' => 'nav_id']);
    }

    public function getCurrentPage()
    {
        return $this->hasOne(NavItemPage::class, ['id' => 'nav_item_type_id']);
    }
}
