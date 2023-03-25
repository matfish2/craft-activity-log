<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use matfish\ActivityLog\services\Stats\Stats;

class ResponseCode extends Stats
{
    public function getData(): array
    {
        $records = $this->query()
            ->select([
                'responseCode',
                'COUNT(*) n'
            ])
            ->andWhere('responseCode is not null')
            ->orderBy('responseCode')
            ->groupBy('responseCode')
            ->all();

        $res = [
            '2xx' => 0,
            '3xx' => 0,
            '4xx' => 0,
            '5xx' => 0
        ];

        foreach ($records as $record) {
            $n = (int)$record['n'];
            $resCode = (int)$record['responseCode'];

            if ($resCode >= 200 && $resCode <= 299) {
                $res['2xx'] += $n;
            } elseif ($resCode >= 300 && $resCode <= 399) {
                $res['3xx'] += $n;
            } elseif ($resCode >= 400 && $resCode <= 499) {
                $res['4xx'] += $n;
            } elseif ($resCode >= 500 && $resCode <= 599) {
                $res['5xx'] += $n;
            }
        }

        return $res;

    }

}