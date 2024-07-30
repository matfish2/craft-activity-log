<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use matfish\ActivityLog\helpers\ActionSegmentsToLabel;
use matfish\ActivityLog\services\Stats\OthersTrait;
use matfish\ActivityLog\services\Stats\Stats;

class Actions extends Stats
{
    use OthersTrait;

    private const MAX_CATEGORIES = 8;

    protected function getData(): array
    {
        $res = $this->query()
            ->select(['{{%activitylog}}.[[actionSegments]]', 'count(*) n'])
            ->andWhere('{{%activitylog}}.[[isAction]]=1')
            ->leftJoin('{{%users}}', '{{%users}}.[[id]]={{%activitylog}}.[[userId]]')
            ->leftJoin('{{%activitylog_actions}}', '{{%activitylog_actions}}.[[action]]={{%activitylog}}.[[actionSegments]]')
            ->orderBy('count(*) DESC')
            ->groupBy('[[actionSegments]]')->all();

        $res = array_map(static function ($x) {
            $x['name'] = ActionSegmentsToLabel::convert($x['actionSegments']);
            return $x;
        }, $res);

        $resWithOther = $this->groupOthers($res);

        return $this->toKeyValuePairs($resWithOther, 'name', 'n');
    }
}