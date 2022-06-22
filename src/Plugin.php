<?php

namespace matfish\ActivityLog;

use matfish\ActivityLog\services\RecordRequest;
use matfish\ActivityLog\models\Settings;
use craft\base\Plugin as BasePlugin;
use Craft;

class Plugin extends BasePlugin
{
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;

    public function init()
    {
        parent::init();

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

}
