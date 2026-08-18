<?php

use app\assets\AdminAsset;
use kartik\dialog\Dialog;

/* @var $this yii\web\View */
/* @var $content string */

AdminAsset::register($this);
$this->registerCssFile('@web/css/administrator-dashboard.css?v=20260817-1', [
    'depends' => [AdminAsset::class],
]);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->title ?? 'Painel Administrativo' ?></title>

    <?= $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>

<body id="page-top" class="administrator-shell">
    <?php $this->beginBody() ?>

    <?php
    echo Dialog::widget([
        'libName' => 'bs4',
    ]);
    ?>

    <div id="wrapper">

        <?= $this->render('partesadminsemjquery/_sidebar') ?>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">
                <?= $this->render('partesadminsemjquery/_topbar') ?>

                <div class="container-fluid">
                    <?= $content ?>
                </div>
            </div>

            <?= $this->render('partesadminsemjquery/_footer') ?>

        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
