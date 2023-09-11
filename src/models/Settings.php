<?php namespace matfish\ActivityLog\models;

use craft\base\Model;

class Settings extends Model
{
    public $recordSitePageRequests = true;
    public $recordSiteAjaxRequests = true;

    public $recordCpPageRequests = true;
    public $recordCpAjaxRequests = false;

    public $recordOnlyActions = false;

    public $requestFilter = null;

    public $viewFilters = [];

    public function rules(): array
    {
        return [
            [['recordSitePageRequests', 'recordSiteAjaxRequests', 'recordCpPageRequests', 'recordCpAjaxRequests', 'recordOnlyActions'], 'boolean']
        ];
    }
}
