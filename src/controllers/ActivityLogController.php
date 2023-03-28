<?php

namespace matfish\ActivityLog\controllers;

use Craft;
use craft\base\WidgetInterface;
use craft\elements\User;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\records\Site;
use craft\web\assets\dashboard\DashboardAsset;
use matfish\ActivityLog\ActivityLogAssetBundle;
use matfish\ActivityLog\records\ActivityLogAction;
use matfish\ActivityLog\records\ActivityLogWidget;
use matfish\ActivityLog\services\Stats\WidgetsHandler;
use matfish\ActivityLog\services\VueTablesActivityLogRetriever;
use matfish\ActivityLog\widgets\DailyRequestsWidget;
use matfish\ActivityLog\widgets\ExecTimeWidget;
use matfish\ActivityLog\widgets\RequestPerUserWidget;
use matfish\ActivityLog\widgets\ResponseCodeWidget;
use matfish\ActivityLog\widgets\VerbsWidget;
use yii\web\Response;
use craft\helpers\Component as ComponentHelper;

class ActivityLogController extends \craft\web\Controller
{

    public function actionStats(): Response
    {
        $dashboardService = Craft::$app->getDashboard();
        $view = $this->getView();

        // Assemble the list of available widget types
        $widgetTypes = [
            DailyRequestsWidget::class,
            ExecTimeWidget::class,
            ResponseCodeWidget::class,
            VerbsWidget::class,
            RequestPerUserWidget::class
        ];

        $widgetTypeInfo = [];

        foreach ($widgetTypes as $widgetType) {
            /** @var string|WidgetInterface $widgetType */
            /** @phpstan-var class-string<WidgetInterface>|WidgetInterface $widgetType */
            if (!$widgetType::isSelectable()) {
                continue;
            }

            $view->startJsBuffer();
            $widget = $dashboardService->createWidget($widgetType);
            $settingsHtml = $view->namespaceInputs(function () use ($widget) {
                return (string)$widget->getSettingsHtml();
            }, '__NAMESPACE__');
            $settingsJs = (string)$view->clearJsBuffer(false);

            $class = get_class($widget);
            $cls = explode("\\", $class);
            $cl = str_replace("Widget", "", last($cls));

            $widgetTypeInfo[$class] = [
                'exists' => (bool)ActivityLogWidget::findOne(['type' => $cl]),
                'iconSvg' => $this->_getWidgetIconSvg($widget),
                'name' => $widget::displayName(),
                'maxColspan' => $widget::maxColspan(),
                'settingsHtml' => $settingsHtml,
                'settingsJs' => $settingsJs,
                'selectable' => true,
            ];
        }

        // Sort them by name
        ArrayHelper::multisort($widgetTypeInfo, 'name');

        $variables = [];

        // Assemble the list of existing widgets
        $variables['widgets'] = [];
        $widgets = (new WidgetsHandler)->getAll();
        $allWidgetJs = '';

        foreach ($widgets as $widget) {
            $view->startJsBuffer();
            $info = $this->_getWidgetInfo($widget);
            $widgetJs = $view->clearJsBuffer(false);

            if ($info === false) {
                continue;
            }

            // If this widget type didn't come back in our getAllWidgetTypes() call, add it now
            if (!isset($widgetTypeInfo[$info['type']])) {
                $widgetTypeInfo[$info['type']] = [
                    'name' => $widget::displayName(),
                    'maxColspan' => $widget::maxColspan(),
                    'selectable' => false,
                ];
            }

            $variables['widgets'][] = $info;

            $allWidgetJs .= 'new Craft.Widget("#widget' . $widget->id . '", ' .
                Json::encode($info['settingsHtml']) . ', ' .
                'function(){' . $info['settingsJs'] . '}' .
                ");\n";

            if (!empty($widgetJs)) {
                // Allow any widget JS to execute *after* we've created the Craft.Widget instance
                $allWidgetJs .= $widgetJs . "\n";
            }
        }

        // Include all the JS and CSS stuff
        $view->registerAssetBundle(DashboardAsset::class);
        $view->registerJsWithVars(
            fn($widgetTypeInfo) => "window.dashboard = new Craft.Dashboard($widgetTypeInfo)",
            [$widgetTypeInfo]
        );
        $view->registerJs($allWidgetJs);

        $variables['widgetTypes'] = $widgetTypeInfo;
        $variables['sites'] = Site::find()->select(['id', 'name'])->all();
        $variables['users'] = array_map(function ($user) {
            return [
                'id' => $user['id'],
                'name' => $user['fullName'] ?: $user['username']
            ];
        }, User::find()->select(['id', 'username', 'fullName'])->all());

        return $this->renderTemplate('activity-logs/stats', $variables);
    }

    public function actionInitialData(): Response
    {
        $res = [
            'sites' => Site::find()->select(['id', 'name'])->all(),
            'svgPath' => $this->getSvgPath(),
            'actions' => ActivityLogAction::find()->all()
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


    /**
     * Returns the info about a widget required to display its body and settings in the Dashboard.
     *
     * @param WidgetInterface $widget
     * @return array|false
     */
    private function _getWidgetInfo(WidgetInterface $widget): array|false
    {
        $view = $this->getView();

        // Get the body HTML
        $widgetBodyHtml = $widget->getBodyHtml();

        if ($widgetBodyHtml === null) {
            return false;
        }

        // Get the settings HTML + JS
        $view->startJsBuffer();
        $settingsHtml = $view->namespaceInputs(function () use ($widget) {
            return (string)$widget->getSettingsHtml();
        }, "widget$widget->id-settings");
        $settingsJs = $view->clearJsBuffer(false);

        // Get the colspan (limited to the widget type's max allowed colspan)
        $colspan = ($widget->colspan ?: 1);

        if (($maxColspan = $widget::maxColspan()) && $colspan > $maxColspan) {
            $colspan = $maxColspan;
        }

        return [
            'id' => $widget->id,
            'type' => get_class($widget),
            'colspan' => $colspan,
            'title' => $widget->getTitle(),
            'subtitle' => $widget->getSubtitle(),
            'name' => $widget->displayName(),
            'bodyHtml' => $widgetBodyHtml,
            'settingsHtml' => $settingsHtml,
            'settingsJs' => (string)$settingsJs,
        ];
    }

    /**
     * Returns a widget type’s SVG icon.
     *
     * @param WidgetInterface $widget
     * @return string
     */
    private function _getWidgetIconSvg(WidgetInterface $widget): string
    {
        return ComponentHelper::iconSvg($widget::icon(), $widget::displayName());
    }

    private function _generateWidgets(array $widgetTypes)
    {
        return array_map(function ($widgetType) {
            return ComponentHelper::createComponent($widgetType, WidgetInterface::class);
        }, $widgetTypes);
    }
}