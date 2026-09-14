<?php

use kartik\alert\Alert;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\Doacao */

?>


<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Doacao', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
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
    <div class="col-md-12">
        <?= $form->field($model, 'participacao_id')->dropDownList($participacoes, [
            'id' => 'doacao-participacao-id',
            'prompt' => 'Selecione a participacao',
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'evento_id')->widget(DepDrop::class, [
            'options' => ['id' => 'doacao-evento-id'],
            'pluginOptions' => [
                'depends' => ['doacao-participacao-id'],
                'placeholder' => 'Selecione um evento',
                'url' => Url::to(['/administrator/doacao/eventos-by-participacao']),
                'initialize' => true,
                'allowClear' => true,
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'tipo_doacao_id')->dropDownList($tipoDoacao, [
            'prompt' => 'Selecione o tipo de doacao',
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'file')->widget(FileInput::class, [
            'options' => ['accept' => '.jpg,.jpeg,.png,.gif,.pdf'],
            'pluginOptions' => [
                'showCaption' => false,
                'showRemove' => false,
                'showUpload' => false,
                'browseClass' => 'btn btn-primary btn-block',
                'browseIcon' => '<i class="fas fa-paperclip"></i> ',
                'browseLabel' => 'Anexar arquivo',
                'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
                'maxFileSize' => 5120,
                'overwriteInitial' => true,
                'initialPreview' => $model->arquivo ? [Yii::getAlias('@web') . '/imagens/doacoes/' . $model->arquivo] : [],
                'initialPreviewAsData' => true,
                'initialPreviewConfig' => $model->arquivo ? [[
                    'caption' => $model->arquivo,
                ]] : [],
            ],
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'status')->dropDownList(\app\modules\common\models\Doacao::getStatusList()) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'motivo_reprovado')->textarea(['rows' => 3]) ?>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'cpf_snapshot')->textInput(['readonly' => true]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'edicao_snapshot')->textInput(['readonly' => true]) ?>
    </div>
</div>

<div class="form-group mt-3">
    <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<JS
$('form').on('afterValidate', function () {
    $('.form-group').each(function () {
        if ($(this).hasClass('has-error')) {
            $(this).find('.form-control').addClass('is-invalid');
        }
    });
});
JS);
?>
