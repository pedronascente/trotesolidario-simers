<?php

use app\modules\common\models\Doacao;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\Doacao */

$isUpdate = !$model->isNewRecord;
?>

<div class="container-fluid">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'participacao_id')->dropDownList($participacoes, [
                'id' => 'doacao-participacao-id',
                'prompt' => 'Selecione a participacao',
                'class' => 'form-control',
                'disabled' => $isUpdate,
            ]) ?>
            <?php if ($isUpdate): ?>
                <?= Html::activeHiddenInput($model, 'participacao_id') ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'tipo_doacao_id')->dropDownList($tipoDoacao, [
                'prompt' => 'Selecione o tipo de doacao',
                'class' => 'form-control',
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'evento_id')->widget(DepDrop::class, [
                'options' => [
                    'id' => 'doacao-evento-id',
                    'class' => 'form-control',
                ],
                'pluginOptions' => [
                    'depends' => ['doacao-participacao-id'],
                    'placeholder' => 'Selecione um evento',
                    'url' => Url::to(['/participante/doacao/eventos-by-participacao']),
                    'initialize' => true,
                    'allowClear' => true,
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList([
                Doacao::STATUS_PENDENTE => 'Pendente',
                Doacao::STATUS_REJEITADA => 'Rejeitada',
            ], [
                'disabled' => true,
                'class' => 'form-control',
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'file')->widget(FileInput::class, [
                'options' => ['accept' => 'image/*,.pdf'],
                'pluginOptions' => [
                    'showCaption' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'browseClass' => 'btn btn-primary btn-block',
                    'browseIcon' => '<i class="fas fa-camera"></i> ',
                    'browseLabel' => 'Anexar comprovante',
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
                    'maxFileSize' => 5120,
                    'overwriteInitial' => false,
                    'initialPreview' => $model->arquivo ? [Yii::getAlias('@web') . '/imagens/doacoes/' . $model->arquivo] : [],
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => [],
                ],
            ])->label('Comprovante da doacao'); ?>
        </div>
    </div>

    <?php if ($model->arquivo): ?>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="small text-muted mb-1">Arquivo atual</div>
                <?= Html::a('Abrir comprovante atual', Yii::getAlias('@web') . '/imagens/doacoes/' . $model->arquivo, [
                    'target' => '_blank',
                    'data-pjax' => '0',
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'motivo_reprovado')->textarea([
                'rows' => 3,
                'readonly' => true,
                'class' => 'form-control',
            ]) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

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
