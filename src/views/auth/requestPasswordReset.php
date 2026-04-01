<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Recuperar senha';
?>

<div class="login-box">
    <div class="mb-4 text-center">
        <h3 class="mb-2">Recuperar senha</h3>
        <p class="text-muted mb-0">Informe seu e-mail para receber o link de redefinição.</p>
    </div>

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?= $form->field($model, 'email')->textInput([
        'autofocus' => true,
        'placeholder' => 'seuemail@exemplo.com',
    ]) ?>

    <div class="form-group mt-4">
        <?= Html::submitButton('Enviar link', ['class' => 'btn btn-primary btn-block']) ?>
    </div>

    <div class="text-center mt-3">
        <?= Html::a('Voltar ao login', ['/auth/login'], ['class' => 'text-primary small']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>