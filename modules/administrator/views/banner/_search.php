<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model app\modules\common\models\BannerSearchModel */
    /* @var $form yii\widgets\ActiveForm */
    ?>

<div class="banner-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'posicao') ?>

    <?= $form->field($model, 'img_mob') ?>

    <?= $form->field($model, 'img_dsk') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
