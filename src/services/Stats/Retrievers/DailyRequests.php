<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use matfish\ActivityLog\services\Stats\Stats;
use Carbon\Carbon;

class DailyRequests extends Stats
{

    public function getData(): array
    {
        $res =  $this->query()
            ->select([
                'DATE(createdAt) d',
                'count(*) n'
            ])
            ->orderBy('DATE(createdAt)')
            ->groupBy('DATE(createdAt)')->all();

        return $this->toKeyValuePairs($res,'d','n');
    }

    protected function transformLabels($labels) : array {
        return array_map(static function($label) {
            return Carbon::parse($label)->format('d/m');
        },$labels);
    }

}