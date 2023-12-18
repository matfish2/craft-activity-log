<?php

namespace matfish\ActivityLog\controllers;

use craft\db\Query;
use craft\elements\User;
use craft\records\Site;
use matfish\ActivityLog\ActivityLogAssetBundle;
use matfish\ActivityLog\services\VueTablesActivityLogRetriever;
use yii\web\Response;
use matfish\ActivityLog\services\ActionSegmentsToLabel;

class ActivityLogController extends \craft\web\Controller
{
    public function actionInitialData(): Response
    {
        $actions = (new Query())->from('{{%activitylog}}')
            ->where('actionSegments IS NOT NULL')
            ->select('actionSegments')
            ->groupBy('actionSegments')
            ->all();

        $actions = array_map(function ($action) {
            return $action['actionSegments'];
        }, $actions);

        $namedActions = [
            '["users","login"]' => 'Login',
            '["users","logout"]' => 'Logout',
            '["users","impersonate"]' => 'Impersonate User',
            '["elements","apply-draft"]' => 'Apply Draft',
            '["elements","save"]' => 'Save Element',
            '["users","save-user"]' => 'Save User',
            '["assets","generate-transform"]' => 'Generate Transform',
            '["plugins","save-plugin-settings"]' => 'Save Plugin Settings',
            '["sections","save-section"]' => 'Save Section'
        ];

        $namedActionsIds = array_keys($namedActions);

        $actions = array_map(function ($action) use ($namedActionsIds, $namedActions) {
            if (in_array($action, $namedActionsIds, true)) {
                return [
                    'id' => $action,
                    'text' => $namedActions[$action]
                ];
            }
            return [
                'id' => $action,
                'text' => ActionSegmentsToLabel::convert($action)
            ];
        }, $actions);

        usort($actions, function ($a, $b) {
            return $a['text'][0] > $b['text'][0] ? -1 : 1;
        });

        $res = [
            'sites' => Site::find()->select(['id', 'name'])->all(),
            'svgPath' => $this->getSvgPath(),
            'actions' => $actions
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

        $res = array_map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->fullName ?? $user->username
            ];
        }, User::find()->search($q)->all());

        return $this->asJson($res);
    }


    private function getSvgPath(): string
    {
        $view = \Craft::$app->view;
        $bundle = ActivityLogAssetBundle::register($view);

        return $bundle->baseUrl . '/svg/';
    }
}