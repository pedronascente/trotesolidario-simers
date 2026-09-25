<?php

use app\modules\common\services\DoacaoArquivoStorage;
use kartik\alert\Alert;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\Doacao */

$arquivoDisponivel = DoacaoArquivoStorage::resolve($model->arquivo) !== null;
$arquivoUrl = $arquivoDisponivel ? Url::to(['arquivo', 'id' => $model->id]) : null;
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

<?php if ($model->isNewRecord): ?>
    <div class="alert alert-info" role="alert">
        <strong><i class="fas fa-info-circle" aria-hidden="true"></i> Quando uma doação pode ser registrada?</strong>
        O administrador pode registrar uma doação somente para um participante com participação ativa
        em um trote que ainda não foi encerrado.
    </div>

    <?php if (empty($participacoes)): ?>
        <div class="alert alert-warning" role="alert">
            <strong>Nenhum participante disponível.</strong>
            Cadastre ou ative uma participação em um trote não encerrado para registrar a doação.
        </div>
    <?php endif; ?>
<?php endif; ?>

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
                'maxFileSize' => 2048,
                'overwriteInitial' => true,
                'initialPreview' => $arquivoUrl ? [$arquivoUrl] : [],
                'initialPreviewAsData' => true,
                'initialPreviewConfig' => $arquivoUrl ? [[
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
