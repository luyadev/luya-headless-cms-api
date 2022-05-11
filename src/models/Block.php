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
     * @param integer $id The context id, the cms_nav_item_page_block_item unique id
     * @param string $context admin or frontend
     * @param mixed $pageObject
     * @return BlockInterface
     */
    public function getClassObject($id = null, $context = null, $pageObject = null)
    {
        return self::createObject($this->class, $this->id, $id, $context, $pageObject);
    }

    /**
     * Creates the block object and stores the object within a static block container.
     *
     * @param string $class
     * @param integer $blockId The id of the cms_block table
     * @param integer $id The context id, the cms_nav_item_page_block_item unique id
     * @param string $context admin or frontend
     * @param mixed $pageObject
     * @return \luya\cms\base\BlockInterface
     */
    public static function createObject($class, $blockId, $id, $context, $pageObject = null)
    {
        if (!class_exists($class)) {
            return false;
        }

        /** @var BlockInterface $object */
        $object = Yii::createObject([
            'class' => $class,
        ]);

        $object->setEnvOption('id', $id);
        $object->setEnvOption('blockId', $blockId);
        $object->setEnvOption('context', $context);
        $object->setEnvOption('pageObject', $pageObject);

        $object->setup();

        return $object;
    }
}
