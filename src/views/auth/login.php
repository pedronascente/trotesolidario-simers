<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

$this->title = 'Entrar';
?>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'enableClientValidation' => true,
    'options' => ['class' => 'auth-form', 'novalidate' => true],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'options' => ['class' => 'form-group'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]); ?>

<div class="login-box">
    <div class="auth-intro">
        <h1>Acesse sua conta</h1>
        <p>Entre para acompanhar sua participação no Trote Solidário.</p>
    </div>

    <?= $form->field($model, 'cpf')->widget(MaskedInput::class, [
        'mask' => '999.999.999-99',
        'options' => [
            'class' => 'form-control',
            'inputmode' => 'numeric',
            'autocomplete' => 'username',
            'autofocus' => true,
            'placeholder' => '000.000.000-00',
        ],
        'clientOptions' => [
            'removeMaskOnSubmit' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'password', [
        'inputOptions' => [
            'class' => 'form-control ' . ($model->hasErrors('password') ? 'is-invalid' : ''),
            'autocomplete' => 'current-password',
        ],
    ])->passwordInput(['placeholder' => 'Digite sua senha']) ?>

    <div class="form-group auth-submit-group">
        <?= Html::submitButton('<span>Entrar</span><i class="fas fa-arrow-right" aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-block auth-submit']) ?>
    </div>

    <div class="auth-secondary-action d-flex flex-column align-items-center">
        <?= Html::a('<i class="far fa-question-circle" aria-hidden="true"></i><span>Esqueci minha senha</span>', ['/auth/request-password-reset']) ?>
        <?= Html::a('<i class="fas fa-user-plus" aria-hidden="true"></i><span>Criar minha conta</span>', ['/participante/register/index'], [
            'class' => 'mt-1',
        ]) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<'JS'
$('form').on('afterValidate', function () {
    $('.form-group').each(function () {
        if ($(this).hasClass('has-error')) {
            $(this).find('.form-control').addClass('is-invalid');
        }
    });
});
JS);
?>
