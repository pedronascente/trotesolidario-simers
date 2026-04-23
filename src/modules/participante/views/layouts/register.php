<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AdminAsset;
use app\widgets\Alert;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <?= $this->head() ?>
        <!--Header-->
        <title>Trote Solidario</title>
        <meta name="title" content="Trote Solidario">
        <meta name="description" content="Trote Solidario">
        <meta itemprop="image" content="">
        <!--Fim Header-->
        <!--Twiter-->
        <meta property="twitter:description" content="Trote Solidario">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:title" content="Trote Solidario">
        <meta property="twitter:image" content="">
        <!--Fim Twiter-->
        <!--Facebook-->
        <meta property="og:url" content="">
        <meta property="og:type" content="article">
        <meta property="og:title" content="Trote Solidario">
        <meta property="og:description" content="Trote Solidario">
        <link rel="shortcut icon" href="/img/favicon_trote.png" type="image/x-icon">
        <!--<meta property="og:image" content="/imagens/favicon-32x32.jpg">-->
        <!--Fim Facebook-->
        <!--<link rel="shortcut icon" href="/imagens/favicon.png" type="image/x-icon">-->
        <!-- Custom fonts for this template-->
        <link href="/layoutadmin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <!-- Custom styles for this template-->
        <link href="/layoutadmin/css/sb-admin-2.css" rel="stylesheet">
        <!-- End Google Analytics -->
    </head>
    <body class="bg-gradient-success">
        <?php $this->beginBody() ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        <?php $this->endBody() ?>
                <!-- Bootstrap core JavaScript-->
        <!--<script src="/layoutadmin/vendor/jquery/jquery.min.js"></script>-->
        <script src="/layoutadmin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="/layoutadmin/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="/layoutadmin/js/sb-admin-2.js"></script>
    </body>
</html>
<?php $this->endPage() ?>
