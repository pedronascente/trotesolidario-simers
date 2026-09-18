<?php

namespace app\assets;

use yii\web\AssetBundle;

class ParticipantRegisterAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/participant-register.css',
    ];

    public $depends = [
        AdminAsset::class,
    ];
}
