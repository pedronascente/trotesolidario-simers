<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\modules\common\models\MercadoParceiro */

?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">

    <div class="col-md-12">
        <?= $form->field($model, 'nome_mercado')
            ->textInput([
                'maxlength' => true,
                'autofocus' => true,
            ]) ?>
    </div>

    <div class="col-md-9">
        <?= $form->field($model, 'endereco')
            ->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'numero')
            ->textInput(['maxlength' => true]) ?>
    </div>

</div>

<div class="row">

    <div class="col-md-12">
        <?= $form->field($model, 'bairro')
            ->textInput(['maxlength' => true]) ?>
    </div>

</div>

<?= Html::submitButton('Salvar', [
    'class' => 'btn btn-success'
]) ?>

<?= Html::a('Voltar', ['index'], [
    'class' => 'btn btn-secondary'
]) ?>

<?php ActiveForm::end(); ?>