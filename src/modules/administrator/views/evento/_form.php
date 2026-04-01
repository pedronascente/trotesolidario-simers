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

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'trote_id')->dropDownList($trotes, ['prompt' => 'Selecione um trote']) ?>
    <?php else: ?>
        <?= $form->field($model, 'trote_id')->dropDownList($trotes, ['disabled' => true]) ?>
        <?= Html::activeHiddenInput($model, 'trote_id') ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'data_evento')->input('datetime-local') ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs("$('form').on('afterValidate', function () { $('.form-group').each(function () { if ($(this).hasClass('has-error')) { $(this).find('.form-control').addClass('is-invalid'); } }); });");
?>
