<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\Evento */
/* @var $trotes array */
?>

<div class="evento-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'trote_id')->dropDownList(
            $trotes,
            ['prompt' => 'Selecione um Trote']
        ) ?>

    <?php else: ?>

        <!-- UPDATE: trote fixo (desabilitado) -->
        <?= $form->field($model, 'trote_id')->dropDownList(
            $trotes,
            [
                'disabled' => true,
            ]
        ) ?>

        <!-- garante envio do valor (disabled não envia) -->
        <?= Html::activeHiddenInput($model, 'trote_id') ?>

    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'data_evento')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'ativo')->dropDownList([
                1 => 'Sim',
                0 => 'Não',
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'descricao')->textarea(['rows' => 4]) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php

$this->registerJs("

/* ===============================
VALIDACAO VISUAL DO FORMULARIO
=================================*/

$('form').on('afterValidate', function () {
    $('.form-group').each(function()
    {
        if($(this).hasClass('has-error'))
        {
            $(this).find('.form-control').addClass('is-invalid');
        }
    });
});

");
?>