<?php

use app\modules\common\models\Doacao;
use app\modules\common\models\TipoDoacao;
use app\modules\common\services\DoacaoArquivoStorage;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\Doacao */

$isUpdate = !$model->isNewRecord;
$arquivoDisponivel = DoacaoArquivoStorage::resolve($model->arquivo) !== null;
$arquivoUrl = $arquivoDisponivel ? Url::to(['arquivo', 'id' => $model->id]) : null;
$tipoDoacaoSangueIds = array_map('strval', array_keys(array_filter(
    $tipoDoacao,
    [TipoDoacao::class, 'isNomeSangue']
)));
$tipoDoacaoSangueIdsJson = Json::htmlEncode($tipoDoacaoSangueIds);
$isDoacaoUpdateJs = $isUpdate ? 'true' : 'false';
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
                'prompt' => 'Selecione a participação',
                'class' => 'form-control',
                'disabled' => $isUpdate,
            ]) ?>
            <?php if ($isUpdate): ?>
                <?= Html::activeHiddenInput($model, 'participacao_id') ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'tipo_doacao_id')->dropDownList($tipoDoacao, [
                'prompt' => 'Selecione o tipo de doação',
                'class' => 'form-control',
            ]) ?>
        </div>
    </div>

    <div id="termo-doacao-sangue" class="row" hidden>
        <div class="col-md-12">
            <?= $form->field($model, 'termo_doacao_sangue')->checkbox([
                'value' => 1,
                'uncheck' => 0,
                'checked' => $isUpdate && $model->isTipoDoacaoSangue(),
                'disabled' => $isUpdate,
                'label' => 'Termos: Estou ciente de que a doação deve ser realizada pelo próprio participante, não sendo permitidas doações de terceiros.',
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
            <div class="alert alert-info py-2 mb-2" role="note">
                <strong><i class="fas fa-info-circle" aria-hidden="true"></i> Arquivos permitidos:</strong>
                imagens JPG, JPEG, PNG ou GIF e documentos PDF, com no máximo 2 MB.
            </div>
            <?= $form->field($model, 'file')->widget(FileInput::class, [
                'options' => ['accept' => '.jpg,.jpeg,.png,.gif,.pdf'],
                'pluginOptions' => [
                    'showCaption' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'browseClass' => 'btn btn-primary btn-block',
                    'browseIcon' => '<i class="fas fa-camera"></i> ',
                    'browseLabel' => 'Anexar comprovante',
                    'allowedFileExtensions' => Doacao::COMPROVANTE_EXTENSIONS,
                    'maxFileSize' => (int) (Doacao::COMPROVANTE_MAX_SIZE / 1024),
                    'overwriteInitial' => false,
                    'initialPreview' => $arquivoUrl ? [$arquivoUrl] : [],
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => [],
                ],
            ]); ?>
        </div>
    </div>

    <?php if ($arquivoUrl): ?>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="small text-muted mb-1">Arquivo atual</div>
                <?= Html::a('Abrir comprovante atual', $arquivoUrl, [
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
var tipoDoacaoSangueIds = {$tipoDoacaoSangueIdsJson};
var isDoacaoUpdate = {$isDoacaoUpdateJs};

var syncFieldValidationState = function () {
    $('.form-group').each(function () {
        var hasError = $(this).hasClass('has-error');
        $(this).find('.form-control').toggleClass('is-invalid', hasError);
    });
};

syncFieldValidationState();

var syncTermoDoacaoSangue = function () {
    var isDoacaoSangue = tipoDoacaoSangueIds.indexOf($('#doacao-tipo_doacao_id').val()) !== -1;
    $('#termo-doacao-sangue').prop('hidden', !isDoacaoSangue);

    if (isDoacaoUpdate) {
        $('#doacao-termo_doacao_sangue').prop('checked', isDoacaoSangue);
    } else if (!isDoacaoSangue) {
        $('#doacao-termo_doacao_sangue').prop('checked', false);
    }
};

$('#doacao-tipo_doacao_id').on('change', syncTermoDoacaoSangue);
syncTermoDoacaoSangue();

$('form').on('afterValidate', function () {
    syncFieldValidationState();
});
JS);
?>
