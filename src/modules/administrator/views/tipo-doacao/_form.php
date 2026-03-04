<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\TipoDoacao */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tipo-doacao-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'carga_horaria')->textInput() ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'pontuacao_ranking')->textInput() ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'ativo')->dropDownList([
                1 => 'Sim',
                0 => 'Não',
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'descricao')->textarea(['rows' => 3]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>