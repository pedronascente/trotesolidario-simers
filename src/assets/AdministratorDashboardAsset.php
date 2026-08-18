<?php

namespace app\assets;

use yii\web\AssetBundle;

class AdministratorDashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/administrator-dashboard.css',
    ];

    public $depends = [
        AdministratorThemeAsset::class,
    ];
}
