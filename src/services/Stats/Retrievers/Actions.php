<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use matfish\ActivityLog\services\Stats\OthersTrait;
use matfish\ActivityLog\services\Stats\Stats;

class Actions extends Stats
{
    use OthersTrait;

    private const MAX_CATEGORIES = 8;

    protected function getData(): array
    {
        $res = $this->query()
            ->select(['{{%activitylog_actions}}.[[label]] name', 'count(*) n'])
            ->andWhere('{{%activitylog}}.[[isAction]]=1')
            ->innerJoin('{{%users}}', '{{%users}}.[[id]]={{%activitylog}}.[[userId]]')
            ->innerJoin('{{%activitylog_actions}}', '{{%activitylog_actions}}.[[action]]={{%activitylog}}.[[actionSegments]]')
            ->orderBy('count(*) DESC')
            ->groupBy('[[actionSegments]]')->all();

        $resWithOther = $this->groupOthers($res);

        return $this->toKeyValuePairs($resWithOther, 'name', 'n');
    }
}