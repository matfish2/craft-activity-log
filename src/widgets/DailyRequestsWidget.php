<?php

namespace matfish\ActivityLog\widgets;

use Craft;

class DailyRequestsWidget extends StatsWidget
{

    public ?int $colspan = 3;

    public static function displayName(): string
    {
        return Craft::t('activity-logs', 'Daily Requests');
    }


    public function getName() : string
    {
        return 'daily-requests';
    }
}