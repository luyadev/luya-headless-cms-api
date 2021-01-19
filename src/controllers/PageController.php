<?php

namespace luya\headless\cms\api\controllers;

use luya\headless\cms\api\BaseController;
use luya\headless\cms\api\models\Nav;
use luya\headless\cms\api\models\NavItem;
use luya\helpers\StringHelper;
use luya\traits\CacheableTrait;
use yii\caching\DbDependency;
use yii\web\NotFoundHttpException;

class PageController extends BaseController
{
    use CacheableTrait;

    public function actionIndex($id)
    {
        if (StringHelper::isNummeric($id)) {
            $attribute = 'id';
        } else {
            $attribute = 'alias';
        }

        $navItem = NavItem::find()
            ->with(['currentPage.blocks.block', 'nav.properties.adminProperty'])
            ->where([$attribute => $id])
            ->cache(true, new DbDependency(['sql' => 'select max(timestamp_update) from cms_nav_item']))
            ->one();

        if (!$navItem) {
            throw new NotFoundHttpException("nav item {$id} not found");
        }

        return $this->toResponse($navItem);
    }

    public function actionNav($id, $langId)
    {
        $navItem = NavItem::find()
            ->with(['currentPage.blocks.block', 'nav.properties.adminProperty'])
            ->where(['nav_id' => $id, 'lang_id' => $langId])
            ->one();

        if (!$navItem) {
            throw new NotFoundHttpException("nav {$id} with language {$langId} not found");
        }

        return $this->toResponse($navItem);
    }

    public function actionHome($langId)
    {
        $nav = Nav::findOne(['is_home' => true]);

        if (!$nav) {
            throw new NotFoundHttpException("nav not found");
        }

        $navItem = $nav->getNavItems()->where(['lang_id' => $langId])->one();

        if (!$navItem) {
            throw new NotFoundHttpException("nav item not found.");
        }

        return $this->toResponse($navItem);
    }

    private function toResponse(NavItem $navItem)
    {
        return $this->getOrSetHasCache(['headlessapi', 'navItem', $navItem->id], function() use ($navItem) {
            return [
                'page' => $navItem->toArray(['id', 'nav_id', 'lang_id', 'title', 'alias', 'description', 'keywords', 'title_tag']),
                'placeholders' => $navItem->currentPage->getContent(),
                'layout' => $navItem->currentPage->layout->toArray(['id', 'name']),
                'properties' => $navItem->nav->formatedProperties(),
            ];
        }, 0,  new DbDependency(['sql' => 'select max(timestamp_update) from cms_nav_item']));
    }
}