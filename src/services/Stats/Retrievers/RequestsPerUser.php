<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use matfish\ActivityLog\services\Stats\OthersTrait;
use matfish\ActivityLog\services\Stats\Stats;

class RequestsPerUser extends Stats
{
    use OthersTrait;

    private const MAX_CATEGORIES = 8;

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


}