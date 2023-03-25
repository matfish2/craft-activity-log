<?php


namespace matfish\ActivityLog\controllers;

use Craft;
use craft\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SettingsController extends Controller
{
    public function actionIndex() {
        $variables = [];
        $plugin = Craft::$app->getPlugins()->getPlugin('activity-logs');
        $variables['settings'] = $plugin->getSettings();

        return $this->renderTemplate('activity-logs/settings/settings', $variables);
    }

     /**
     * Saves the settings.
     *
     * @return Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveSettings(): ?Response
    {
        $this->requirePostRequest();
        $settings = Craft::$app->getRequest()->getBodyParam('settings', []);
        $plugin = Craft::$app->getPlugins()->getPlugin('activity-logs');

        if ($plugin === null) {
            throw new NotFoundHttpException('Plugin not found');
        }

        if (!Craft::$app->getPlugins()->savePluginSettings($plugin, $settings)) {
            Craft::$app->getSession()->setError(Craft::t('app', 'Couldn’t save plugin settings.'));

            // Send the plugin back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'plugin' => $plugin
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Plugin settings saved.'));

        return $this->redirectToPostedUrl();
    }
}