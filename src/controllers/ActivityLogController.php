<?php

namespace matfish\ActivityLog\controllers;

use craft\elements\User;
use craft\records\Site;
use matfish\ActivityLog\ActivityLogAssetBundle;
use matfish\ActivityLog\services\VueTablesActivityLogRetriever;
use yii\web\Response;

class ActivityLogController extends \craft\web\Controller
{
    public function actionInitialData(): Response
    {
        $res  = [
            'sites'=>Site::find()->select(['id','name'])->all(),
            'svgPath'=>$this->getSvgPath()

        ];

        return $this->asJson($res);
    }

    public function actionData(): Response
    {
        $res = (new VueTablesActivityLogRetriever())->retrieve();
        return $this->asJson($res);
    }


    public function actionUsers(): Response
    {
        $q = \Craft::$app->request->getQueryParam('q');

        $res = array_map(function($user) {
            return [
                'id'=>$user->id,
                'name'=>$user->fullName ?? $user->username
            ];
        },User::find()->search($q)->all()) ;

        return $this->asJson($res);
    }


    private function getSvgPath() : string
    {
        $view = \Craft::$app->view;
        $bundle = ActivityLogAssetBundle::register($view);

        return $bundle->baseUrl . '/svg/';
    }
}