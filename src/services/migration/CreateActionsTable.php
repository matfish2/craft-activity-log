<?php


namespace matfish\ActivityLog\services\migration;


use craft\db\Migration;
use matfish\ActivityLog\records\ActivityLogAction;

class CreateActionsTable
{
    public function execute(Migration $migration) {
              $migration->createTable('{{%activitylog_actions}}', [
                'id' => $migration->primaryKey()->notNull(),
                'action' => $migration->string()->notNull(),
                'label' => $migration->string()->notNull(),
                'native' => $migration->boolean()->notNull(),
                'createdAt' => $migration->timestamp()
            ]);

            $actions = [
                [
                    'action' => '["users","login"]',
                    'label' => 'Login'
                ],
                [
                    'action' => '["users","logout"]',
                    'label' => 'Logout'
                ],
                [
                    'action' => '["users","impersonate"]',
                    'label' => 'Impersonate User'
                ],
                [
                    'action' => '["elements","apply-draft"]',
                    'label' => 'Apply Draft'
                ],
                [
                    'action' => '["elements","save"]',
                    'label' => 'Save Element'
                ],
                [
                    'action' => '["users","save-user"]',
                    'label' => 'Save User'
                ],
                [
                    'action' => '["assets","generate-transform"]',
                    'label' => 'Generate Transform'
                ],
                [
                    'action' => '["plugins","save-plugin-settings"]',
                    'label' => 'Save Plugin Settings'
                ],
                [
                    'action' => '["sections","save-section"]',
                    'label' => 'Save Section'
                ]
            ];

            foreach ($actions as $action) {
                $r = new ActivityLogAction();
                $r->action = $action['action'];
                $r->label = $action['label'];
                $r->native = true;
                $r->save();
            }
    }
}