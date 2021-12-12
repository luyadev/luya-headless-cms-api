<?php

namespace luya\headless\cms\api\controllers;

use luya\headless\cms\api\BaseController;
use luya\headless\cms\api\models\Container;
use luya\web\filters\ResponseCache;
use Yii;
use yii\caching\DbDependency;
use yii\data\ActiveDataProvider;

class MenuController extends BaseController
{
    /**
     * {@inheritDoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cacheFilter'] = [
            'class' => ResponseCache::class,
            'only' => ['index'],
            'dependency' => [
                'class' => DbDependency::class,
                'sql' => 'SELECT timestamp_update FROM cms_nav_item WHERE lang_id=:lang_id',
                'params' => [':lang_id' => Yii::$app->request->get('langId', 0)]
            ],
            'variations' => Yii::$app->request->get()
        ];
        return $behaviors;
    }

    public function actionIndex($langId, $onlyVisible = 0)
    {
        $data = [];

        $query = Container::find()
            ->joinWith(['items' => function($q) use ($langId) {
                $q->andOnCondition(['lang_id' => $langId])->joinWith(['nav']);
            }]);

        if ($onlyVisible) {
            $query->andWhere(['is_hidden' => false]);
        }

        foreach ($query->all() as $container) {
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
