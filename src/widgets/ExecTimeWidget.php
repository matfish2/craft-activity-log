<?php

namespace matfish\ActivityLog\widgets;


use Craft;

class ExecTimeWidget extends StatsWidget
{
     public ?int $colspan = 3;

    public static function displayName(): string
    {
        return Craft::t('activity-logs', 'Average Execution Time');
    }


    public function getName() : string
    {
        return 'exec-time';
    }
}