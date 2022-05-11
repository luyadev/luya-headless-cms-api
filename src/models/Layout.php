<?php

namespace luya\headless\cms\api\models;

use luya\behaviors\JsonBehavior;
use yii\db\ActiveRecord;

/**
 * Class Layout
 * @package luya\headless\cms\api\models
 *
 * @property int $id
 * @property string $name
 * @property array|null $json_config
 * @property string $view_file
 */
class Layout extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_layout';
    }

    public function behaviors()
    {
        return [
            [
                'class' => JsonBehavior::class,
                'attributes' => ['json_config'],
            ],
        ];
    }

    public function getPlaceholdersList()
    {
        $list = [];
        foreach ($this->json_config['placeholders'] as $row) {
            foreach ($row as $col) {
                $list[] = $col['var'];
            }
        }

        return $list;
    }
}
