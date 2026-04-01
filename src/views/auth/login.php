<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'options' => ['class' => 'form-group'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]); ?>

<div class="login-box">
    <?= $form->field($model, 'cpf')->widget(MaskedInput::class, [
        'mask' => '999.999.999-99',
        'clientOptions' => [
            'removeMaskOnSubmit' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'password', [
        'inputOptions' => [
            'class' => 'form-control ' . ($model->hasErrors('password') ? 'is-invalid' : ''),
        ],
    ])->passwordInput(['placeholder' => 'Senha']) ?>

    <div class="form-group">
        <?= Html::submitButton('Entrar', ['class' => 'btn btn-primary btn-block']) ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <div>
            <?= Html::a('Esqueci minha senha', ['/auth/request-password-reset'], [
                'class' => 'text-primary small',
            ]) ?>
        </div>
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