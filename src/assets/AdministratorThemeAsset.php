<?php

namespace app\assets;

use yii\web\AssetBundle;

class AdministratorThemeAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/administrator-theme.css',
    ];

    public $depends = [
        AdminAsset::class,
    ];
}
