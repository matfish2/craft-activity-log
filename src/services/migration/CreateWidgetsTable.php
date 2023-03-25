<?php

namespace matfish\ActivityLog\services\migration;

use craft\db\Migration;
use matfish\ActivityLog\records\ActivityLogWidget;

class CreateWidgetsTable
{
    public function execute(Migration $migration)
    {
        $migration->createTable('{{%activitylog_widgets}}', [
            'id' => $migration->primaryKey()->notNull(),
            'userId' => $migration->integer()->unsigned(),
            'type' => $migration->string()->notNull(),
            'sortOrder' => $migration->tinyInteger()->unsigned(),
            'colspan' => $migration->tinyInteger()->unsigned()->null(),
            'settings' => $migration->text()->notNull(),
            'enabled' => $migration->boolean(),
            'dateCreated' => $migration->timestamp(),
            'dateUpdated' => $migration->timestamp(),
            'uuid' => $migration->uid()
        ]);

        $data = [
            [
                'type' => 'DailyRequests',
                'sortOrder' => 1,
                'colspan' => 3,
            ],
            [
                'type' => 'ExecTime',
                'sortOrder' => 2,
                'colspan' => 2,
            ],
            [
                'type' => 'ResponseCode',
                'sortOrder' => 3,
                'colspan' => 1,
            ],
            [
                'type' => 'Verbs',
                'sortOrder' => 5,
                'colspan' => 1,
            ]
        ];

        foreach ($data as $widget) {
            $r = new ActivityLogWidget();
            $r->type = $widget['type'];
            $r->sortOrder = $widget['sortOrder'];
            $r->colspan = $widget['colspan'];
            $r->enabled = true;
            $r->settings = "[]";
            $r->save();
        }
    }
}