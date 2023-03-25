<?php

namespace matfish\ActivityLog\widgets;

use craft\base\Widget;
use craft\base\WidgetInterface;
use craft\base\WidgetTrait;

abstract class StatsWidget extends Widget implements WidgetInterface
{
    use WidgetTrait;

    public function getBodyHtml(): ?string
    {
        $name = $this->getName();

        return "<activity-logs-stats-widget name='$name'></activity-logs-stats-widget>";
    }

}