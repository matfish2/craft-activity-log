<?php

namespace matfish\ActivityLog\migrations;

use Craft;
use craft\db\Migration;

/**
 * m250417_134140_migrate_from_craft_3 migration.
 */
class m250417_134140_migrate_from_craft_3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // if a dateCreated column exists, rename it to createdAt
        if ($this->db->getTableSchema('{{%activitylog}}')->getColumn('dateCreated')) {
            $this->renameColumn('{{%activitylog}}', 'dateCreated', 'createdAt');
        }

        // if a dateUpdated column exists, drop it
        if ($this->db->getTableSchema('{{%activitylog}}')->getColumn('dateUpdated')) {
            $this->dropColumn('{{%activitylog}}', 'dateUpdated');
        }

        // if a uid column exists, drop it
        if ($this->db->getTableSchema('{{%activitylog}}')->getColumn('uid')) {
            $this->dropColumn('{{%activitylog}}', 'uid');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m250417_134140_migrate_from_craft_3 cannot be reverted.\n";
        return false;
    }
}
