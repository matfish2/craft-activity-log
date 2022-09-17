<?php

namespace matfish\ActivityLog;

use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\web\UrlManager;
use matfish\ActivityLog\services\RecordRequest;
use matfish\ActivityLog\models\Settings;
use craft\base\Plugin as BasePlugin;
use Craft;
use yii\base\Event;
use yii\web\NotFoundHttpException;

class Plugin extends BasePlugin
{
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;
    public string $schemaVersion = '1.0.1';

    public function init()
    {
        parent::init();
        $this->_registerCpRoutes();

        if (Craft::$app->request->isCpRequest) {
            $this->controllerNamespace = 'matfish\\ActivityLog\\controllers';
        } elseif (Craft::$app->request->isConsoleRequest) {
            $this->controllerNamespace = 'matfish\\ActivityLog\\controllers\\console';
        }

        if (!$this->db->tableExists('{{%activitylog}}')) {
            return;
        }

        if ($this->shouldRecord()) {
            try {
                (new RecordRequest(Craft::$app->request))->record();
            } catch (\Throwable $e) {
                echo $e->getMessage();
            }
        }
    }

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): null|string
    {
        return \Craft::$app->getView()->renderTemplate(
            'activity-logs/settings',
            ['settings' => $this->getSettings()]
        );
    }

    private function isLoginRequest($request): bool
    {
        $segments = $request->getActionSegments();
        return $segments[0] === 'users' && $segments[1] === 'login';
    }

    private function shouldRecord(): bool
    {
        $request = Craft::$app->request;

        if ($request->isConsoleRequest) {
            return false;
        }

        $isCp = $request->isCpRequest;
        $isAjax = $this->isAjax($request);
        $settings = self::getInstance()->getSettings();
        $true = $settings->recordOnlyActions ? $request->isActionRequest : true;

        if ($isCp) {
            if ($isAjax && ($settings->recordCpAjaxRequests || $this->isLoginRequest($request))) {
                return $true;
            }

            if (!$isAjax && $settings->recordCpPageRequests) {
                return $true;
            }
        }

        if (!$isCp) {
            if ($isAjax && $settings->recordSiteAjaxRequests) {
                return $true;
            }

            if (!$isAjax && $settings->recordSitePageRequests) {
                return $true;
            }
        }

        return false;

    }

    protected function isAjax($request): bool
    {
        return $request->isAjax || str_contains($request->headers->get('Accept'), 'application/json');
    }

    /**
     * Register CP routes.
     */
    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event): void {
            $rules = [
                'settings/activity-logs' => 'activity-logs/settings/index',
                'settings/activity-logs/actions' => 'activity-logs/actions/index',
                'settings/activity-logs/settings' => 'activity-logs/settings/settings',
            ];

            $event->rules = array_merge($event->rules, $rules);
        });
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse(): mixed
    {
        $url = UrlHelper::cpUrl('settings/activity-logs');

        Craft::$app->controller->redirect($url);

        return '';
    }
}
