<?php


namespace matfish\ActivityLog\migrations;


use craft\db\Migration;
use matfish\ActivityLog\records\ActivityLogAction;

class m220702_144330_add_actions_table extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%activitylog_actions}}')) {
            $this->createTable('{{%activitylog_actions}}', [
                'id' => $this->primaryKey()->notNull(),
                'action' => $this->string()->notNull(),
                'label' => $this->string()->notNull(),
                'native' => $this->boolean()->notNull(),
                'createdAt' => $this->timestamp()
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

    public function safeDown()
    {
        if ($this->db->tableExists('{{%activitylog_actions}}')) {
            $this->dropTable('{{%activitylog_actions}}');
        }
    }
}