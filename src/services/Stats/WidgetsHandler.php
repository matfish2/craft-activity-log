<?php

namespace matfish\ActivityLog\services\Stats;

use Craft;
use craft\base\Widget;
use craft\base\WidgetInterface;
use craft\db\Query;
use craft\helpers\Component;
use matfish\ActivityLog\records\ActivityLogWidget;
use Throwable;

class WidgetsHandler
{
    public function getAll(): array
    {
        $results = $this->_createWidgetsQuery()
            ->orderBy(['sortOrder' => SORT_ASC])
            ->all();

        $widgets = [];

        foreach ($results as $result) {
            $widgets[] = $this->createWidget($result);
        }

        return $widgets;
    }

    public function save($type)
    {

        if (ActivityLogWidget::findOne(['type' => $type])) {
            return false;
        }

        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            $widgetRecord = new ActivityLogWidget;

            $widgetRecord->type = $type;
            $widgetRecord->settings = "[]";

            // Set the sortOrder
            $maxSortOrder = (new Query())
                ->from(['{{%activitylog_widgets}}'])
                ->max('[[sortOrder]]');

            $widgetRecord->sortOrder = $maxSortOrder + 1;

            $widgetRecord->save(false);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    public function changeColspan($id, $colspan) {
        $model = ActivityLogWidget::findOne(['id' => $id]);
        $model->colspan = $colspan;
        $model->save();
    }

    private function _createWidgetsQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'dateCreated',
                'dateUpdated',
                'colspan',
                'type',
                'settings',
            ])
            ->from(['{{%activitylog_widgets}}']);
    }

    public function reorderWidgets($widgetIds): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            foreach ($widgetIds as $widgetOrder => $widgetId) {
                $widgetRecord = $this->_getWidgetRecordById($widgetId);
                $widgetRecord->sortOrder = $widgetOrder + 1;
                $widgetRecord->save();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;

    }

    private function createWidget(mixed $config): Widget
    {
        $type = $config['type'];
        $config['type'] = "matfish\ActivityLog\widgets\\{$type}Widget";

        return Component::createComponent($config, WidgetInterface::class);
    }

    private function _getWidgetRecordById(mixed $widgetId): ActivityLogWidget
    {
        return ActivityLogWidget::findOne([
            'id' => $widgetId,
        ]);
    }
}