<?php

namespace matfish\ActivityLog\records;

use craft\db\ActiveRecord;

class ActivityLogWidget extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%activitylog_widgets}}';
    }
}