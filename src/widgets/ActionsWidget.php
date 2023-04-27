<?php

namespace matfish\ActivityLog\widgets;

use Craft;

class ActionsWidget extends StatsWidget
{
    public ?int $colspan = 1;

    public static function displayName(): string
    {
        return Craft::t('activity-logs', 'Actions');
    }


    public function getName(): string
    {
        return 'actions';
    }
}