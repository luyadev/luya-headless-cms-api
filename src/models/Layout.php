<?php

namespace luya\headless\cms\models;

use yii\db\ActiveRecord;

class Layout extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_layout';
    }
}