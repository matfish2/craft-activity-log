<?php


namespace matfish\ActivityLog\records;


use craft\db\ActiveRecord;

class ActivityLogAction extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%activitylog_actions}}';
    }
}