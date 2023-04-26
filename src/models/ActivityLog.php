<?php


namespace matfish\ActivityLog\models;


use craft\base\Model;

class ActivityLog extends Model
{
    public $userId;
    public $execTime;
    public $url;
    public $method;
    public $query;
    public $payload;
    public $ip;
    public $userAgent;
    public $isAjax;
    public $siteId;
    public $isCp;
    public $isAction;
    public $actionSegments;
}