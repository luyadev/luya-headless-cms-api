<?php

namespace luya\headless\cms\api\models;

use yii\db\ActiveRecord;

class Nav extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_nav';
    }

    public function extraFields()
    {
        return array_merge(parent::extraFields(), ['navItems']);
    }

    public function getNavItems()
    {
        return $this->hasMany(NavItem::class, ['nav_id' => 'id']);
    }
}