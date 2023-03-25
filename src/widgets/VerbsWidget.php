<?php

namespace matfish\ActivityLog\widgets;

use Craft;

class VerbsWidget extends StatsWidget
{
    public static function displayName(): string
    {
        return Craft::t('activity-logs', 'Request Method');
    }

    public function getName(): string
    {
        return 'request-verbs';
    }
}