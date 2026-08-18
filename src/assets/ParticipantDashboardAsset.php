<?php

namespace app\assets;

use yii\web\AssetBundle;

class ParticipantDashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/participant-dashboard.css',
    ];

    public $depends = [
        AdminAsset::class,
    ];
}
