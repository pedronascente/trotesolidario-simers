<?php

namespace app\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'layoutadmin/vendor/fontawesome-free/css/all.min.css',
        'layoutadmin/css/sb-admin-2.css',
        'layoutadmin/css/admin-custom.css',
        'css/project-buttons.css',
    ];

    public $js = [
        'layoutadmin/vendor/jquery-easing/jquery.easing.min.js',
        'layoutadmin/js/sb-admin-2.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'yii\grid\GridViewAsset',
    ];
}
