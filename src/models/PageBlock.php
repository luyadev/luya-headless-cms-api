<?php

namespace luya\headless\cms\api\models;

use luya\behaviors\JsonBehavior;
use yii\db\ActiveRecord;

class PageBlock extends ActiveRecord
{
    public static function tableName()
    {
        return 'cms_nav_item_page_block_item';
    }

    public function getPage()
    {
        return $this->hasOne(NavItemPage::class, ['id' => 'nav_item_page_id']);
    }

    public function behaviors()
    {
        return [
            [
                'class' => JsonBehavior::class,
                'attributes' => ['json_config_values', 'json_config_cfg_values'],
            ]
        ];
    }

    public function getBlock()
    {
        return $this->hasOne(Block::class, ['id' => 'block_id']);
    }

    public function getEnsuredValues()
    {
        $values = [];
        foreach ($this->block->classObject->getConfigVarsExport() as $var) {
            $values[$var['var']] = $this->json_config_values && array_key_exists($var['var'], $this->json_config_values) ? $this->json_config_values[$var['var']] : null;
        }

        return $values;
    }

    public function getEnsuredConfigs()
    {
        $values = [];
        foreach ($this->block->classObject->getConfigCfgsExport() as $var) {
            $values[$var['var']] = $this->json_config_cfg_values && array_key_exists($var['var'], $this->json_config_cfg_values) ? $this->json_config_cfg_values[$var['var']] : null;
        }

        return $values;
    }
}