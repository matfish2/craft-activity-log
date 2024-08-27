<?php

namespace matfish\ActivityLog\migrations;

use Craft;
use craft\db\Migration;

/**
 * m240827_121219_modify_ip_column_to_account_for_ipv6 migration.
 */
class m240827_121219_modify_ip_column_to_account_for_ipv6 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // https://github.com/matfish2/craft-activity-log/issues/16
        // make IP column longer to account for IPv6. varchar(50) instead of varchar(20)
        $this->alterColumn('{{%activitylog}}', 'ip', $this->string(50));

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m240827_121219_modify_ip_column_to_account_for_ipv6 cannot be reverted.\n";
        return false;
    }
}
