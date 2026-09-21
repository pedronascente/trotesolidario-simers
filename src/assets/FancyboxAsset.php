<?php

namespace app\assets;

use yii\web\AssetBundle;

class FancyboxAsset extends AssetBundle
{
    public $css = [
        'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css',
    ];

    public $js = [
        'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js',
    ];

    public $depends = [
        AdminAsset::class,
    ];
}
