<?php

use app\assets\GestaoCustosAsset;
use app\modules\common\models\DistribuicaoCusto;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

GestaoCustosAsset::register($this);
$rows = [];
foreach ($distribuicoes as $distribuicao) {
    $rows[] = $distribuicao instanceof DistribuicaoCusto ? $distribuicao->attributes : $distribuicao;
}
$rows = $rows ?: [['universidade_id' => '', 'valor_previsto' => '', 'observacao' => '']];
?>

<div class="gestao-custos-page">
    <div class="gc-page-heading">
        <div><span class="gc-heading-icon"><i class="fas fa-coins"></i></span><h1><?= Html::encode($this->title) ?></h1><p>Registre o orçamento e sua distribuição por universidade.</p></div>
        <?= Html::a('<i class="fas fa-arrow-left mr-1"></i> Voltar', ['index', 'trote_id' => $model->trote_id], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php $form = ActiveForm::begin(['id' => 'gestao-custo-form']); ?>
    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <section class="gc-panel">
        <header><i class="fas fa-file-invoice-dollar"></i><div><h2>Informações gerais</h2><p>Preencha os dados principais do custo.</p></div></header>
        <div class="gc-panel-body">
            <div class="row">
                <div class="col-lg-4">
                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'trote_id')->dropDownList($trotes, ['prompt' => 'Selecione o trote']) ?>
                    <?php else: ?>
                        <?= $form->field($model, 'trote_id')->dropDownList($trotes, ['disabled' => true]) ?>
                        <?= Html::activeHiddenInput($model, 'trote_id') ?>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4"><?= $form->field($model, 'tipo_categoria_custo_id')->dropDownList($tiposCategoria, ['prompt' => 'Selecione a categoria']) ?></div>
                <div class="col-lg-4"><?= $form->field($model, 'valor_previsto')->textInput(['class' => 'form-control gc-money', 'inputmode' => 'decimal', 'placeholder' => '0,00']) ?></div>
            </div>
            <div class="row">
                <div class="col-lg-6"><?= $form->field($model, 'descricao')->textarea(['rows' => 3, 'maxlength' => 500]) ?></div>
                <div class="col-lg-6"><?= $form->field($model, 'observacao')->textarea(['rows' => 3, 'maxlength' => 500]) ?></div>
            </div>
        </div>
    </section>

    <section class="gc-panel">
        <header class="gc-distribution-header"><i class="fas fa-map-marker-alt"></i><div><h2>Distribuição por universidade / cidade</h2><p>A soma é opcional, mas não pode ultrapassar o valor total previsto.</p></div><button type="button" class="btn btn-outline-success btn-sm" id="add-distribution"><i class="fas fa-plus mr-1"></i> Adicionar distribuição</button></header>
        <div class="gc-panel-body">
            <div class="table-responsive">
                <table class="table gc-distribution-table">
                    <thead><tr><th>Universidade / cidade</th><th>Valor</th><th>Observações</th><th><span class="sr-only">Ações</span></th></tr></thead>
                    <tbody id="distribution-rows">
                    <?php foreach ($rows as $index => $row): ?>
                        <tr>
                            <?= Html::hiddenInput("DistribuicaoCusto[$index][id]", $row['id'] ?? '') ?>
                            <td><?= Html::dropDownList("DistribuicaoCusto[$index][universidade_id]", $row['universidade_id'] ?? '', $universidades, ['class' => 'form-control', 'prompt' => 'Selecione']) ?></td>
                            <td><?= Html::textInput("DistribuicaoCusto[$index][valor_previsto]", $row['valor_previsto'] ?? '', ['class' => 'form-control gc-distribution-value', 'inputmode' => 'decimal', 'placeholder' => '0,00']) ?></td>
                            <td><?= Html::textInput("DistribuicaoCusto[$index][observacao]", $row['observacao'] ?? '', ['class' => 'form-control', 'maxlength' => 500, 'placeholder' => 'Informações adicionais']) ?></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-distribution" aria-label="Remover distribuição"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr><th>Total da distribuição</th><th id="distribution-total">R$ 0,00</th><th colspan="2"></th></tr></tfoot>
                </table>
            </div>
        </div>
    </section>

    <div class="gc-form-actions">
        <?= Html::a('Cancelar', ['index', 'trote_id' => $model->trote_id], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::submitButton('<i class="fas fa-save mr-1"></i> Salvar custo', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$options = Json::htmlEncode($universidades);
$rowIndex = count($rows);
$this->registerJs(<<<JS
var gcIndex = {$rowIndex};
var gcUniversidades = {$options};
function gcOptions() {
    var html = '<option value="">Selecione</option>';
    Object.keys(gcUniversidades).forEach(function(id) { html += '<option value="' + id + '">' + $('<div>').text(gcUniversidades[id]).html() + '</option>'; });
    return html;
}
function gcUpdateTotal() {
    var total = 0;
    $('.gc-distribution-value').each(function() {
        var raw = String($(this).val() || '');
        raw = raw.indexOf(',') >= 0 ? raw.replace(/\./g, '').replace(',', '.') : raw;
        total += parseFloat(raw) || 0;
    });
    $('#distribution-total').text(total.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'}));
}
$('#add-distribution').on('click', function() {
    var prefix = 'DistribuicaoCusto[' + gcIndex + ']';
    $('#distribution-rows').append('<tr><td><select class="form-control" name="' + prefix + '[universidade_id]">' + gcOptions() + '</select></td><td><input class="form-control gc-distribution-value" inputmode="decimal" placeholder="0,00" name="' + prefix + '[valor_previsto]"></td><td><input class="form-control" maxlength="500" placeholder="Informações adicionais" name="' + prefix + '[observacao]"></td><td><button type="button" class="btn btn-sm btn-outline-danger remove-distribution" aria-label="Remover distribuição"><i class="fas fa-trash"></i></button></td></tr>');
    gcIndex++;
});
$(document).on('click', '.remove-distribution', function() { $(this).closest('tr').remove(); gcUpdateTotal(); });
$(document).on('input', '.gc-distribution-value', gcUpdateTotal);
gcUpdateTotal();
JS);
?>
