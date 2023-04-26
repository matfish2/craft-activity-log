<?php

namespace matfish\ActivityLog\services;

use Carbon\Carbon;
use craft\db\Query;

class VueTablesActivityLogRetriever
{
    public function retrieve(): array
    {
        $req = \Craft::$app->request;

        $q = (new Query())->from('{{%activitylog}}')
            ->leftJoin('{{%users}}', '{{%users}}.[[id]]={{%activitylog}}.[[userId]]')
            ->select([
                '{{%activitylog}}.*',
                'CONCAT({{%users}}.[[firstName]], " ",{{%users}}.[[lastName]]) [[fullName]]',
                '{{%users}}.[[userName]]'

            ]);
        $filters = $req->getQueryParam('query');
        $createdAt = $req->getQueryParam('dateCreated');
        $createdAt = $createdAt ? json_decode($createdAt, true) : null;

        $filters = $filters ? json_decode($filters, true) : [];
        $createdAtStart = $createdAt ? $createdAt['start'] : Carbon::today()->format('d/m/Y');
        $createdAtEnd = $createdAt ? $createdAt['end'] : Carbon::today()->format('d/m/Y');
        $start = Carbon::createFromFormat('d/m/Y', $createdAtStart)->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::createFromFormat('d/m/Y', $createdAtEnd)->endOfDay()->format('Y-m-d H:i:s');

        $action = $req->getQueryParam('actionSegments');
        $action = $action ? json_decode($action,true) : null;

        $page = $req->getQueryParam('page') ?? 1;
        $perPage = $req->getQueryParam('limit');
        $orderCol = $req->getQueryParam('orderBy') ?? 'createdAt';
        $orderDir = $req->getQueryParam('ascending') === '1' ? SORT_ASC : SORT_DESC;

        $q->where("{{%activitylog}}.[[dateCreated]]>='{$start}'");
        $q->andWhere("{{%activitylog}}.[[dateCreated]]<='{$end}'");

        foreach ($filters as $key => $value) {
            if ($key === 'url') {
                $q->andWhere("[[$key]] LIKE '%{$value}%' OR [[query]] LIKE '%{$value}%'");
            } elseif ($key === 'responseCode') {
                $valueEnd = $value + 99;
                $q->andWhere("[[$key]]>=$value AND [[$key]]<=$valueEnd");
            }  elseif ($key === 'userId') {
                $q->andWhere("{{%users}}.[[username]] LIKE '%$value%' OR CONCAT({{%users}}.[[firstName]], ' ',{{%users}}.[[lastName]])  LIKE '%$value%'");
            } else {
                $q->andWhere("[[$key]]='{$value}'");
            }
        }

        if ($action) {
            $type = $action['type'];
            $value = $action['q'];

            if ($value==='allActions') {
                $q->andWhere("[[isAction]]=1");
            } elseif ($type==='equal') {
                $q->andWhere("[[actionSegments]]='$value'");
            } else {
                $q->andWhere("[[actionSegments]] LIKE '%$value%'");
            }
        }

        $q->orderBy([$orderCol => $orderDir]);

        $count = $q->count();

        $q->limit($perPage);
        $q->offset(($page - 1) * $perPage);

        return ['count' => $count, 'data' => $q->all()];
    }
}