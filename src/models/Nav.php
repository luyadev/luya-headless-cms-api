<?php

namespace luya\headless\cms\api\models;

use luya\admin\models\Property as ModelsProperty;
use luya\cms\models\Property;
use yii\db\ActiveRecord;

/**
 * Class Nav
 * @package luya\headless\cms\api\models
 * @property int $id
 * @property int $nav_container_id
 * @property int $parent_nav_id
 * @property int $sort_index
 * @property bool $is_deleted
 * @property bool $is_hidden
 * @property bool $is_home
 * @property bool $is_offline
 * @property bool $is_draft
 * @property string $layout_file
 * @property int $publish_from
 * @property int $publish_till
 *
 * @property NavItem[] $navItems
 * @property Property[] $properties
 */
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

        foreach (ModelsProperty::find()->all() as $adminProp) {
            $varName = $adminProp->createObject(null)->varName();

            if (!array_key_exists($varName, $values)) {
                $values[$varName] = null;
            }
        }

        return (object)$values;
    }
}
