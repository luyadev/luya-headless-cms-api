<?php

namespace luya\headless\cms\api\controllers;

use luya\headless\cms\api\BaseController;
use luya\headless\cms\api\models\Nav;
use luya\headless\cms\api\models\NavItem;
use luya\helpers\StringHelper;
use yii\web\NotFoundHttpException;

class PageController extends BaseController
{
    public function actionIndex($id)
    {
        if (StringHelper::isNummeric($id)) {
            $attribute = 'id';
        } else {
            $attribute = 'alias';
        }
        $navItem = NavItem::find()
            ->with(['currentPage.blocks.block'])
            ->where([$attribute => $id])
            ->one();

        if (!$navItem) {
            throw new NotFoundHttpException("nav item not found");
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
        return [
            'page' => $navItem->toArray(['id', 'nav_id', 'lang_id', 'title', 'alias', 'description', 'keywords', 'title_tag']),
            'placeholders' => $navItem->currentPage->getContent(),
            'layout' => $navItem->currentPage->layout->toArray(['id', 'name']),
        ];
    }
    // expand=currentPage.blocks,currentPage.layout
}