<?php

namespace luya\headless\cms\api\controllers;

use luya\headless\cms\api\BaseController;
use luya\headless\cms\api\models\Container;
use yii\data\ActiveDataProvider;

class MenuController extends BaseController
{
    public function actionIndex($langId)
    {
        $data = [];
        foreach (Container::find()->joinWith(['items.nav'])
            ->andWhere(['lang_id' => $langId, 'is_offline' => false, 'is_draft' => false])
            ->all() as $container) {
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