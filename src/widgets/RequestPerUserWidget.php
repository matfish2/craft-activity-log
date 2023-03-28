<?php

namespace matfish\ActivityLog\widgets;

use Craft;

class RequestPerUserWidget extends StatsWidget
{
    public ?int $colspan = 1;

    public static function displayName(): string
    {
        return Craft::t('activity-logs', 'Requests per user');
    }

    public function getName(): string
    {
        return 'requests-per-user';
    }
}