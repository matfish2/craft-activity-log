<?php

namespace matfish\ActivityLog;

use craft\web\AssetBundle;

class ActivityLogAssetBundle extends AssetBundle
{
    public function init()
    {
        parent::init();

        // define the path that your publishable resources live
        $this->sourcePath = '@matfish/ActivityLog/assets';

        // define the dependencies
        $this->depends = [
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'compiled/activity-log.min.js?v=3',
        ];

        $this->css = [
            'compiled/activity-log.min.css'
        ];
    }
}