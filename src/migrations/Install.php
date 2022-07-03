<?php

namespace matfish\ActivityLog\migrations;


use craft\db\Migration;
use matfish\ActivityLog\services\CreateActionsTable;

class Install extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%activitylog}}')) {
            $this->createTable('{{%activitylog}}', [
                'id' => $this->primaryKey()->notNull(),
                'url' => $this->string()->notNull(),
                'query' => $this->longText()->null(),
                'payload' => $this->longText()->null(),
                'userId' => $this->integer()->null(),
                'execTime' => $this->float()->null(),
                'ip' => $this->string(20),
                'userAgent' => $this->string(),
                'isAjax' => $this->boolean(),
                'method' => $this->string(10),
                'siteId' => $this->integer(),
                'isCp' => $this->boolean(),
                'isAction' => $this->boolean(),
                'actionSegments' => $this->string()->null(),
                'responseCode' => $this->smallInteger(),
                'createdAt' => $this->timestamp()
            ]);

            $this->createIndex('activityLogCreatedAt_idx', '{{%activitylog}}', 'createdAt');
            $this->createIndex('activityLogUserId_idx', '{{%activitylog}}', 'userId');

        }
        if (!$this->db->tableExists('{{%activitylog_actions}}')) {
            (new CreateActionsTable())->execute($this);
        }

    }

    public function safeDown()
    {
        if ($this->db->tableExists('{{%activitylog}}')) {
            $this->dropTable('{{%activitylog}}');
        }

        if ($this->db->tableExists('{{%activitylog_actions}}')) {
            $this->dropTable('{{%activitylog_actions}}');
        }
    }
}