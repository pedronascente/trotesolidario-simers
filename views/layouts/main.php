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
        <title>Hub de Solidariedade</title>
        <meta name="title" content="Hub de Solidariedade">
        <meta name="description" content="Hub de Solidariedade">
        <meta itemprop="image" content="">
        <!--Fim Header-->
        <!--Twiter-->
        <meta property="twitter:description" content="Hub de Solidariedade">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:title" content="Hub de Solidariedade">
        <meta property="twitter:image" content="">
        <!--Fim Twiter-->
        <!--Facebook-->
        <meta property="og:url" content="">
        <meta property="og:type" content="article">
        <meta property="og:title" content="Hub de Solidariedade">
        <meta property="og:description" content="Hub de Solidariedade">
        <meta property="og:image" itemprop="image" content="">
        <!--Fim Facebook-->
        <link rel="shortcut icon" href="/imagens/fav_icon.png" type="image/x-icon">
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.1/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <!-- Google Analytics -->
        <!-- Global site tag (gtag.js) - Google Analytics -->
<!--        <script async src="https://www.googletagmanager.com/gtag/js?id=G-4MZSSQY96N"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-4MZSSQY96N');
        </script>-->
        <!-- End Google Analytics -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="/css/styles.css" rel="stylesheet" />
    </head>
    <body id="page-top">
        <?php $this->beginBody() ?>

        <?= Alert::widget() ?>
        <?= $content ?>
        
        <?php $this->endBody() ?>
        <!-- Bootstrap core JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Third party plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
        <!-- Contact form JS-->
        <script src="/js/jqBootstrapValidation.js"></script>
        <script src="/js/contact_me.js"></script>
        <!-- Core theme JS-->
        <script src="/js/scripts.js"></script>
        <footer class="footer py-4" style="background-color: #343a40;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-5 text-lg-left">
                        <div class="row text-left">
                            <div class="col-md-12">
                                <p style="color: #fff;">Rua Coronel Corte Real, 975 Petrópolis - Porto Alegre</p>
                                <p style="color: #fff;">(51) 3027 - 3737</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 ">
                        <a style="color:#fff" href="https://simers.org.br/politica-privacidade" target="_blank">Politica de privacidade</a>
                        <br>
                        <a style="color:#fff" href="https://simers.org.br/seus-direitos" target="_blank">Seus direitos</a>
                        <br>
                        <a style="color:#fff" href="https://simers.org.br/sites-simers" target="_blank">Sites Simers</a>
                    </div>
                    <div class="col-lg-4 my-3 my-lg-0">
                        <div class="row text-right">
                            <div class="col-md-12">
                                <!-- TODO: redes sociais do simers -->
<!--                                <a class="btn btn-dark btn-social mx-2" href="https://www.facebook.com/simers.rs/" target="_blank"><i class="fab fa-facebook fa-w-10"></i></a>
                                <a class="btn btn-dark btn-social mx-2" href="https://www.instagram.com/simers_rs/" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a class="btn btn-dark btn-social mx-2" href="https://www.youtube.com/channel/UCyr842A7X7aWQnDW6R0BNAw" target="_blank"><i class="fab fa-youtube"></i></a>-->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p style="color:#fff;" class="text-right">Simers 2021 Todos os direitos reservados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
<?php $this->endPage() ?>
