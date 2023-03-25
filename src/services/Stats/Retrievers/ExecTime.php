<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use Carbon\Carbon;
use matfish\ActivityLog\services\Stats\Stats;

class ExecTime extends Stats
{

    protected function getData(): array
    {
        $res = $this->query()
            ->select([
                'DATE(createdAt) d',
                'ROUND(avg(execTime)*1000) ae'
            ])
            ->orderBy('DATE(createdAt)')
            ->groupBy('DATE(createdAt)')->all();

        return $this->toKeyValuePairs($res, 'd', 'ae');
    }

    protected function transformLabels($labels): array
    {
        return array_map(static function ($label) {
            return Carbon::parse($label)->format('d/m');
        }, $labels);
    }
}