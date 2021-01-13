<?php

namespace luya\headless\cms\api\models;

use luya\cms\models\Property;
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

    public function getProperties()
    {
        return $this->hasMany(Property::class, ['nav_id' => 'id']);
    }

    public function formatedProperties()
    {
        $values = [];
        foreach ($this->properties as $property) {
            $values[$property->object->varName()] = [
                'id' => $property->id,
                'value' => $property->object->getValue(),
                'default_value' => $property->object->defaultValue(),
                'admin_value' => $property->object->getAdminValue(),
            ];
        }

        return $values;
    }
}