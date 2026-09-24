<?php

use app\modules\common\models\Trote;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Trote */
/* @var $form yii\widgets\ActiveForm */

$currentYear = (int) date('Y');
$editionOptions = [];

foreach ([1, 2,3] as $semester) {
    for ($year = $currentYear; $year >= $currentYear - 6; $year--) {
        $edition = $year . '.' . $semester;
        $editionOptions[$edition] = $edition;
    }
}
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'options' => ['class' => 'form-group'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'invalid-feedback d-block'],
    ],
]); ?>

<div class="row">
    <div class="col-md-8">
        <?= $form->field($model, 'titulo')->textInput(['maxlength' => 200,'minlength' => 2,]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'edicao')->dropDownList($editionOptions, ['prompt' => 'Selecione a edicao',]) ?>
    </div>
</div>

<?= $form->field($model, 'descricao')->textarea(['rows' => 4]) ?>

<div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'status')->dropDownList(Trote::getStatusList()) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'data_inicio')->input('date', ['value' => $model->data_inicio ? date('Y-m-d', strtotime($model->data_inicio)) : '',]) ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'data_fim')->input('date', ['value' => $model->data_fim ? date('Y-m-d', strtotime($model->data_fim)) : '',]) ?>
    </div>
</div>

<div class="form-group mt-3">
    <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<'JS'
var syncFieldValidationState = function () {
    $(".trote-form .form-group").each(function () {
        var $group = $(this);
        var hasError = $group.hasClass("has-error");

        $group.find(".form-control").toggleClass("is-invalid", hasError);
    });
};

syncFieldValidationState();

$('form').on('afterValidate', function () {
    syncFieldValidationState();
});
JS);
?>