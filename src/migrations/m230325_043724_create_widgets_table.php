<?php

namespace matfish\ActivityLog\migrations;

use Craft;
use craft\db\Migration;
use matfish\ActivityLog\services\migration\CreateWidgetsTable;

/**
 * m230325_043724_create_widgets_table migration.
 */
class m230325_043724_create_widgets_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        if (!$this->db->tableExists('{{%activitylog_widgets}}')) {
            (new CreateWidgetsTable())->execute($this);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230325_043724_create_widgets_table cannot be reverted.\n";
        return false;
    }
}
