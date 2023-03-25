<?php

namespace matfish\ActivityLog\controllers;

use craft\helpers\Json;
use craft\web\Controller;
use matfish\ActivityLog\records\ActivityLogWidget;
use matfish\ActivityLog\services\Stats\StatsRetriever;
use matfish\ActivityLog\services\Stats\WidgetsHandler;
use Carbon\Carbon;

class StatisticsController extends Controller
{

    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $r = \Craft::$app->request;
        $name = $r->getQueryParam('name');
        $isCp = $r->getQueryParam('isCp');
        $isAjax = $r->getQueryParam('isAjax');
        $siteId = $r->getQueryParam('siteId');
        $userId = $r->getQueryParam('userId');

        $filters = [
            'start' => Carbon::createFromFormat('d/m/Y', $r->getQueryParam('start'))->startOfDay()->format('Y-m-d H:i:s'),
            'end' => Carbon::createFromFormat('d/m/Y', $r->getQueryParam('end'))->endOfDay()->format('Y-m-d H:i:s')
        ];

        if ($isCp !== null) {
            $filters['isCp'] = $isCp;
        }

        if ($isAjax !== null) {
            $filters['isAjax'] = $isAjax;
        }

        if ($siteId !== null) {
            $filters['siteId'] = $siteId;
        }

        if ($userId !== null) {
            $filters['userId'] = $userId;
        }

        $res = new StatsRetriever($name, $filters);

        return $this->asJson($res->get());
    }

    public function actionCreateWidget()
    {
        $type = \Craft::$app->request->getBodyParam('type');

        $ps = explode('\\', $type);
        $type = last($ps);
        $type = str_replace("Widget", "", $type);

        (new WidgetsHandler())->save($type);

        return $this->redirect('/admin/activity-logs/stats');
    }

    public function actionReorderWidgets()
    {
        $widgetIds = Json::decode($this->request->getRequiredBodyParam('ids'));

        (new WidgetsHandler())->reorderWidgets($widgetIds);

        return $this->asSuccess();
    }

    public function actionDeleteWidget()
    {
        $id = \Craft::$app->request->getBodyParam('id');
        ActivityLogWidget::findOne(['id' => $id])->delete();

        return $this->asSuccess();
    }

    public function actionChangeWidgetColspan()
    {
        $widgetId = $this->request->getRequiredBodyParam('id');
        $colspan = $this->request->getRequiredBodyParam('colspan');

        (new WidgetsHandler())->changeColspan($widgetId, $colspan);

        return $this->asSuccess();


    }
}