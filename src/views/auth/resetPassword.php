<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Redefinir senha';
?>

<div class="login-box">
    <div class="mb-4 text-center">
        <h3 class="mb-2">Redefinir senha</h3>
        <p class="text-muted mb-0">Cadastre sua nova senha para voltar a acessar o sistema.</p>
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

    <?= $form->field($model, 'password')->passwordInput([
        'autofocus' => true,
        'placeholder' => 'Nova senha',
    ]) ?>

    <?= $form->field($model, 'password_confirmation')->passwordInput([
        'placeholder' => 'Confirmação da senha',
    ]) ?>

    <div class="form-group mt-4">
        <?= Html::submitButton('Salvar nova senha', ['class' => 'btn btn-primary btn-block']) ?>
    </div>

    <div class="text-center mt-3">
        <?= Html::a('Voltar ao login', ['/auth/login'], ['class' => 'text-primary small']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>