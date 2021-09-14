<?php

use app\modules\admin\models\Helper;
$countbanner = 0;
$countbanneractive = 0;
?>
<?= Yii::$app->controller->renderPartial('_menusite'); ?>

<!-- Masthead-->
<section style="<?= Helper::isMobile()? 'padding: 7rem 0;': ''?>">
    <div id="banners" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php foreach($banners as $banner): ?>
                <li data-target="#banners" data-slide-to="<?= $countbanneractive?>" class="<?= $countbanneractive==0?"active":""?>"></li>
              <?php $countbanneractive++; ?>
            <?php            endforeach;?>
        </ol>
        <div class="carousel-inner">
            <?php foreach($banners as $banner): ?>
            
            <div class="carousel-item <?= $countbanner==0?"active":""?>">
                <img src="/img/banners/<?= Helper::isMobile()?$banner->banner_mbl:$banner->banner_dsk?>" class="img-fluid">
            </div>
            <?php $countbanner++; ?>
            <?php            endforeach;?>
        </div>
        <a class="carousel-control-prev" href="#banners" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#banners" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</section>

<!--<section style="background-size: cover;
         background-image: url('/imagens/<?php $capa ?>');
         background-repeat: no-repeat;
         background-attachment: scroll;
         background-position: center center;">
</section>-->
<!-- QUEM SOMOS -->
<?= Yii::$app->controller->renderPartial('_quemsomos'); ?>
<!-- PROJETOS -->
<?= Yii::$app->controller->renderPartial('_projetos',['projetos'=>$projects]); ?>
<!-- PARCEIROS -->
<?= Yii::$app->controller->renderPartial('_parceiros'); ?>

<!-- NOSSOS PARCEIROS-->
<?= Yii::$app->controller->renderPartial('_nossosparceiros'); ?>
<!-- SEJA VOLUNTARIO-->
<?= Yii::$app->controller->renderPartial('_sejavoluntario'); ?>
<?= Yii::$app->controller->renderPartial('_noticias',['noticias'=>$news]); ?>
<?= Yii::$app->controller->renderPartial('_faleconosco'); ?>

