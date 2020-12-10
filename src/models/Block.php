<?php

namespace luya\headless\cms\models;

use yii\db\ActiveRecord;

class Block extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_block';
    }
}