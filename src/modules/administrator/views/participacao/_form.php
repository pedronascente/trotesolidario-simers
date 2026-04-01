<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="participacao-form">
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'user_id')->dropDownList($users, ['prompt' => 'Selecione um usuario']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'trote_id')->dropDownList($trotes, ['prompt' => 'Selecione um trote']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'universidade_id')->dropDownList($universidades, ['prompt' => 'Selecione uma universidade']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'curso')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList(\app\modules\common\models\Participacao::getStatusList(), ['prompt' => 'Selecione']) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
