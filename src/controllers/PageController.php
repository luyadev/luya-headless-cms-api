<?php

namespace luya\headless\cms\controllers;

use luya\headless\cms\BaseController;
use luya\headless\cms\models\NavItem;

class PageController extends BaseController
{
    public function actionIndex($id)
    {
        return NavItem::find()
            ->with(['currentPage'])
            ->where(['id' => $id])
            ->one();
    }
}