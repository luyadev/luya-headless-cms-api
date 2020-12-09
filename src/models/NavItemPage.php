<?php

namespace luya\headless\cms\models;

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
}