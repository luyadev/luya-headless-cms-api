<?php

namespace luya\headless\cms\models;

use luya\behaviors\JsonBehavior;
use yii\db\ActiveRecord;

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
            ]
        ];
    }

    public function getPlaholdersList()
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