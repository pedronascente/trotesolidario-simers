<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model app\modules\common\models\DoacaoSearchModel */
    /* @var $form yii\widgets\ActiveForm */
    ?>

<div class="doacao-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'arquivo') ?>

    <?= $form->field($model, 'instituicao') ?>

    <?= $form->field($model, 'validado') ?>

    <?= $form->field($model, 'usuario_validacao') ?>

    <?php // echo $form->field($model, 'tipo_doacao') 
    ?>

    <?php // echo $form->field($model, 'user_create') 
    ?>

    <?php // echo $form->field($model, 'data_create') 
    ?>

    <?php // echo $form->field($model, 'user_update') 
    ?>

    <?php // echo $form->field($model, 'data_update') 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>