<?php

namespace luya\headless\cms\api\models;

use yii\db\ActiveRecord;

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