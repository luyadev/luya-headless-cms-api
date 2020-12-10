<?php

namespace luya\headless\cms\controllers;

use luya\headless\cms\BaseController;
use luya\headless\cms\models\NavItem;

class PageController extends BaseController
{
    public function actionIndex($id)
    {
        $navItem = NavItem::find()
            ->with(['currentPage.blocks.block'])
            ->where(['id' => $id])
            ->one();


        return [
            'page' => $navItem->toArray(['id', 'nav_id', 'lang_id', 'title', 'alias', 'description', 'keywords', 'title_tag']),
            'placeholders' => $navItem->currentPage->getContent(),
            'layout' => $navItem->currentPage->layout->toArray(['id', 'name']),
        ];
    }

    // expand=currentPage.blocks,currentPage.layout
}