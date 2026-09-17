<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Recuperar senha';
?>

<div class="login-box">
    <div class="auth-intro">
        <span class="auth-intro-icon" aria-hidden="true"><i class="fas fa-lock"></i></span>
        <h1>Recuperar senha</h1>
        <p>Informe o e-mail cadastrado para receber o link de redefinição.</p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'password-reset-request-form',
        'enableClientValidation' => true,
        'options' => ['class' => 'auth-form', 'novalidate' => true],
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
        'type' => 'email',
        'inputmode' => 'email',
        'autocomplete' => 'email',
        'autocapitalize' => 'none',
        'spellcheck' => 'false',
    ]) ?>

    <div class="form-group auth-submit-group">
        <?= Html::submitButton('<span>Enviar link</span><i class="fas fa-paper-plane" aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-block auth-submit']) ?>
    </div>

    <div class="auth-secondary-action">
        <?= Html::a('<i class="fas fa-arrow-left" aria-hidden="true"></i><span>Voltar ao login</span>', ['/auth/login']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
