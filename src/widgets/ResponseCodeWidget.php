<?php

namespace matfish\ActivityLog\widgets;

use Craft;

class ResponseCodeWidget extends StatsWidget
{

    public ?int $colspan = 1;

    public static function displayName(): string
    {
        return Craft::t('activity-logs', 'Response Codes');
    }

    public function getName() : string {
        return 'response-code';
    }
}