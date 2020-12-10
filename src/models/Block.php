<?php

namespace luya\headless\cms\models;

use luya\cms\base\BlockInterface;
use Yii;
use yii\db\ActiveRecord;

class Block extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_block';
    }

    private $_object;

    /**
     * Returns the origin block object based on the current active record entry.
     *
     * @return BlockInterface
     */
    public function getClassObject()
    {
        if ($this->_object === null) {
            $this->_object = Yii::createObject(['class' => $this->class]);
        }

        return $this->_object;
    }
}