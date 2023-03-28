<?php

namespace matfish\ActivityLog\services\Stats\Retrievers;

use matfish\ActivityLog\services\Stats\Stats;

class RequestVerbs extends Stats
{

    protected function getData(): array
    {
        $res = $this->query()->select(['[[method]]', 'count(*) n'])->groupBy('[[method]]')->all();

        return $this->toKeyValuePairs($res, 'method', 'n');
    }

    protected function transformValues($values) : array {
        return array_map(static function($value) {
            return (int) $value;
        }, $values);
    }
}