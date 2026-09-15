<?php

namespace app\assets;

use yii\web\AssetBundle;

class GestaoCustosAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = ['css/gestao-custos.css'];
    public $depends = [AdministratorThemeAsset::class];
}
