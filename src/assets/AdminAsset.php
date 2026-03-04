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
    ];

    public $js = [
        // NÃO carregue jQuery manualmente
        // YiiAsset já faz isso
        'layoutadmin/vendor/bootstrap/js/bootstrap.bundle.min.js',
        'layoutadmin/vendor/jquery-easing/jquery.easing.min.js',
        'layoutadmin/js/sb-admin-2.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',           // jQuery
        'yii\grid\GridViewAsset',     //  yiiGridView()
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}