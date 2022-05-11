<?php

namespace luya\headless\cms\api\models;

use luya\cms\base\BlockInterface;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class Block
 * @package luya\headless\cms\api\models
 *
 * @property int $id
 * @property int $group_id
 * @property string $class
 * @property bool $is_disabled
 *
 * @property-read BlockInterface $classObject
 */
class Block extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_block';
    }

    /**
     * Returns the origin block object based on the current active record entry.
     *
     * @return BlockInterface
     */
    public function getClassObject()
    {
        return Yii::createObject(['class' => $this->class]);
    }
}
