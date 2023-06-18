<?php


namespace matfish\ActivityLog\controllers;


use Craft;
use craft\web\Controller;
use matfish\ActivityLog\records\ActivityLog;
use matfish\ActivityLog\records\ActivityLogAction;

class ActionsController extends Controller
{
    public function actionIndex()
    {
        // 1. Get all natively labelled actions in order to exclude from page
        $native = ActivityLogAction::find()->where('native=1')->all();

        // 2. Get all non-native actions from actions table in order to allow editing
        $actions1 = ActivityLogAction::find()->where('native=0')->all();
        $actions1 = array_map(static function ($action) {
            return [
                'id' => $action->id,
                'action' => $action->action,
                'label' => $action->label,
            ];
        }, $actions1);

        // 3. Merge native and non-native actions in order to exclude from search in logs
        $nonnative = array_map(static function ($action) {
            return "'" . $action['action'] . "'";
        }, $actions1);

        $native = array_map(static function ($action) {
            return "'" . $action->action . "'";
        }, $native);

        $existingActions = array_merge($nonnative, $native);

        // 4. Get all unlabeled actions from activity logs table
        $existingActionsExp = count($existingActions) > 0 ? implode(',', $existingActions) : "'xxx'";
        $actions2 = ActivityLog::find()->select('actionSegments')
            ->where('actionSegments is not null AND actionSegments not in (' . $existingActionsExp . ')')
            ->groupBy('actionSegments')->all();

        $actions2 = array_map(static function ($action) {
            return [
                'id' => null,
                'action' => $action->actionSegments,
                'label' => ''
            ];
        }, $actions2);

        // 5. merge actions from actions table with actions from logs table
        $actions = array_merge($actions1, $actions2);

        return $this->renderTemplate('activity-logs/actions', ['actions' => $actions]);
    }

    /**
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSaveActions(): \yii\web\Response
    {
        $actions = Craft::$app->getRequest()->getBodyParam('actions', []);

        foreach ($actions as $action) {
            $label = trim($action['label']);

            if (!$label) {
                continue;
            }

            $existing = ActivityLogAction::find()->where("action='" . $action['action'] . "'")->all();

            if (count($existing) > 0) {
                $record = $existing[0];
            } else {
                $record = new ActivityLogAction();
                $record->action = $action['action'];
            }

            $record->label = $label;
            $record->native = false;
            $record->save();
        }

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Actions saved.'));

        return $this->redirectToPostedUrl();
    }
}