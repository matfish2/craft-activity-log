<?php

namespace matfish\ActivityLog\migrations;

use craft\db\Migration;
use matfish\ActivityLog\services\migration\CreateActionsTable;

/**
 * m220703_102139_add_actions_table migration.
 */
class m220703_102139_add_actions_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
       if (!$this->db->tableExists('{{%activitylog_actions}}')) {
            (new CreateActionsTable())->execute($this);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        if ($this->db->tableExists('{{%activitylog_actions}}')) {
            $this->dropTable('{{%activitylog_actions}}');
        }

        return true;
    }
}
