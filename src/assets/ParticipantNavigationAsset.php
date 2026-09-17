<?php

namespace app\assets;

use yii\web\AssetBundle;

class ParticipantNavigationAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'layoutadmin/js/participant-navigation.js',
    ];

    public $depends = [
        AdminAsset::class,
    ];
}
