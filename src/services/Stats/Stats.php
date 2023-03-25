<?php

namespace matfish\ActivityLog\services\Stats;

use craft\db\Query;

abstract class Stats
{

    protected array $filters;

    /**
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    abstract protected function getData(): array;

    public function get(): array
    {
        $data = $this->getData();

        return [
            'labels' => $this->transformLabels(array_keys($data)),
            'values' => $this->transformValues(array_values($data))
        ];
    }

    protected function query(): Query
    {
        $q = (new Query())->from('{{%activitylog}}')
            ->where("createdAt>='{$this->filters['start']}'")
            ->andWhere("createdAt<='{$this->filters['end']}'");

        if (isset($this->filters['isCp'])) {
            $q->andWhere("isCp={$this->filters['isCp']}");
        }

        if (isset($this->filters['isAjax'])) {
            $q->andWhere("isAjax={$this->filters['isAjax']}");
        }

        if (isset($this->filters['siteId'])) {
            $q->andWhere("siteId={$this->filters['siteId']}");
        }

        if (isset($this->filters['userId'])) {
            $q->andWhere("userId={$this->filters['userId']}");
        }

        return $q;
    }

    protected function toKeyValuePairs($data, $key, $value): array
    {
        $res = [];

        foreach ($data as $record) {
            $res[$record[$key]] = $record[$value];
        }

        return $res;
    }

    protected function transformLabels(array $labels): array
    {
        return $labels;
    }

    protected function transformValues(array $values): array
    {
        return $values;
    }
}