<?php

namespace matfish\ActivityLog\services\Stats;

trait OthersTrait
{
 private function groupOthers(array $res): array
    {
        if (count($res) <= self::MAX_CATEGORIES) {
            return $res;
        }

        $result = array_slice($res, 0, self::MAX_CATEGORIES);
        $other = array_slice($res, self::MAX_CATEGORIES);

        $otherCount = array_reduce($other, static function ($prev, $current) {
            return $prev + (int)$current['n'];
        }, 0);

        $result[] = [
            'name' => 'Others',
            'n' => $otherCount
        ];

        return $result;
    }
}