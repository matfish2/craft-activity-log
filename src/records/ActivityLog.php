<?php

namespace matfish\ActivityLog\records;


class ActivityLog extends \craft\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%activitylog}}';
    }
}