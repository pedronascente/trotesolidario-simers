<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\Doacao */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'options' => ['enctype' => 'multipart/form-data'],
            ]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'participacao_id')->dropDownList($participacoes, [
                        'prompt' => '- Selecione uma participacao -',
                        'id' => 'doacao-participacao-id',
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'tipo_doacao_id')->dropDownList($tipoDoacao, [
                        'prompt' => '- Selecione um tipo de doacao -',
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'evento_id')->dropDownList([], [
                        'prompt' => '- Selecione um evento -',
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'status')->dropDownList([\app\modules\common\models\Doacao::STATUS_PENDENTE => 'Pendente'], [
                        'disabled' => true,
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'file')->label('Anexo da doacao')->widget(FileInput::class, [
                        'options' => ['accept' => '.jpg,.jpeg,.png,.gif,.pdf'],
                        'pluginOptions' => [
                            'showCaption' => false,
                            'showRemove' => false,
                            'showUpload' => false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'browseIcon' => '<i class="fas fa-paperclip"></i>',
                            'browseLabel' => 'Anexar comprovante de doacao',
                            'allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png', 'pdf'],
                            'maxFileSize' => 5120,
                            'overwriteInitial' => true,
                            'initialPreview' => $model->arquivo ? [Yii::getAlias('@web') . '/imagens/doacoes/' . $model->arquivo] : [],
                            'initialPreviewAsData' => true,
                        ],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?php if ($model->arquivo) : ?>
                        <a href="<?= Yii::getAlias('@web') . '/imagens/doacoes/' . $model->arquivo ?>" target="_blank">Arquivo atual</a>
                    <?php endif; ?>
                </div>
            </div>

            <?= $form->field($model, 'motivo_reprovado')->textarea(['rows' => 3, 'readonly' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$eventosUrl = \yii\helpers\Url::to(['/administrator/doacao/eventos-by-participacao']);
$eventoSelecionado = (int) $model->evento_id;
$this->registerJs(<<<JS
function carregarEventosParticipacao() {
    let participacao = $('#doacao-participacao-id').val();
    let select = $('#doacao-evento_id');
    select.empty();
    select.append('<option value="">- Selecione um evento -</option>');

    if (!participacao) {
        return;
    }

    $.post('$eventosUrl', { depdrop_parents: [participacao] }, function (data) {
        $.each(data.output || [], function (_, item) {
            let selected = item.id == $eventoSelecionado ? 'selected' : '';
            select.append('<option value="' + item.id + '" ' + selected + '>' + item.name + '</option>');
        });
    });
}

$('#doacao-participacao-id').on('change', carregarEventosParticipacao);
$(document).ready(carregarEventosParticipacao);
JS);
?>
