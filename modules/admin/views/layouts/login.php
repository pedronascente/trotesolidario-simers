<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <!--Header-->
        <title>Trote Solídario</title>
        <meta name="title" content="Trote Solídario">
        <meta name="description" content="Trote Solídario">
        <meta itemprop="image" content="/imagens/capa.jpg">
        <!--Fim Header-->
        <!--Twiter-->
        <meta property="twitter:description" content="Trote Solídario">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:title" content="Trote Solídario">
        <meta property="twitter:image" content="/imagens/capa.jpg">
        <!--Fim Twiter-->
        <!--Facebook-->
        <meta property="og:url" content="">
        <meta property="og:type" content="article">
        <meta property="og:title" content="Trote Solídario">
        <meta property="og:description" content="Trote Solídario">
        <link rel="shortcut icon" href="/img/favicon_trote.png" type="image/x-icon">
        <!--<meta property="og:image" content="/imagens/favicon-32x32.jpg">-->
        <!--Fim Facebook-->
        <!--<link rel="shortcut icon" href="/imagens/favicon.png" type="image/x-icon">-->
        <!-- Google fonts-->
        <!-- Google Analytics -->
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-4MZSSQY96N"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-4MZSSQY96N');
        </script>
        <!-- Custom fonts for this template-->
        <link href="/layoutadmin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="/layoutadmin/css/sb-admin-2.css" rel="stylesheet">
        <!-- End Google Analytics -->
    </head>
<body style="background: #eff7ff;">
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
