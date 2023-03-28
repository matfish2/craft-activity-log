<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use matfish\ActivityLog\services\Stats\Stats;

class RequestsPerUser extends Stats
{
    private const MAX_USERS = 8;

    protected function getData(): array
    {
        $res = $this->query()
            ->select(['[[userId]]', "IF(([[fullName]] IS NULL OR [[fullName]]=''), {{%users}}.[[username]], {{%users}}.[[fullName]]) name", 'count(*) n'])
            ->innerJoin('{{%users}}', '{{%users}}.[[id]]={{%activitylog}}.[[userId]]')
            ->orderBy('count(*) DESC')
            ->groupBy('[[userId]]')->all();

        $resWithOther = $this->groupOthers($res);

        return $this->toKeyValuePairs($resWithOther, 'name', 'n');
    }

    protected function transformValues($values): array
    {
        return array_map(static function ($value) {
            return (int)$value;
        }, $values);
    }

    private function groupOthers(array $res): array
    {
        if (count($res) <= self::MAX_USERS) {
            return $res;
        }

        $result = array_slice($res, 0, self::MAX_USERS);
        $other = array_slice($res, self::MAX_USERS + 1);

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