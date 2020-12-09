<?php

namespace luya\headless\cms\controllers;

use luya\headless\cms\BaseController;
use luya\headless\cms\models\Container;
use yii\data\ActiveDataProvider;

class MenuController extends BaseController
{
    public function actionIndex($langId)
    {
        $data = [];
        foreach (Container::find()->joinWith(['items.nav'])->andWhere(['lang_id' => $langId])->all() as $container) {
            $data[$container->alias] = [
                'id' => $container->id,
                'name' => $container->name,
                'alias' => $container->alias,
                'items' => $container->containerToMenu($container),   
            ];
        };

        return $data;
    }

    public function actionContainers()
    {
        return new ActiveDataProvider([
            'query' => Container::find(),
            'pagination' => false,
        ]);
    }
}