<?php

use app\assets\GestaoCustosAsset;
use kartik\alert\Alert;
use yii\helpers\Html;

GestaoCustosAsset::register($this);
$this->title = 'Gestão de custos';
$money = static fn($value) => 'R$ ' . number_format((float) $value, 2, ',', '.');
$maxValue = 0.0;
foreach ($categorias as $categoria) {
    $maxValue = max($maxValue, (float) $categoria->valor_previsto);
}
?>

<div class="gestao-custos-page">
    <?php foreach (['success' => Alert::TYPE_SUCCESS, 'error' => Alert::TYPE_DANGER] as $flash => $type): ?>
        <?php if (Yii::$app->session->hasFlash($flash)): ?>
            <?= Alert::widget(['type' => $type, 'body' => Html::encode(Yii::$app->session->getFlash($flash)), 'delay' => 4000]) ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="gc-page-heading">
        <div><h1>Gestão de custos</h1><p>Acompanhe e controle o orçamento do Trote Solidário.</p></div>
        <div class="gc-heading-actions">
            <?= Html::beginForm(['index'], 'get', ['class' => 'gc-filter-form']) ?>
            <?= Html::dropDownList('trote_id', $troteId, $trotes, ['class' => 'form-control', 'prompt' => 'Todas as edições', 'onchange' => 'this.form.submit()']) ?>
            <?= Html::endForm() ?>
            <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo custo', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="row gc-metrics">
        <div class="col-lg-4 col-md-6"><div class="gc-metric"><span class="gc-metric-icon green"><i class="fas fa-coins"></i></span><div><small>Orçamento previsto</small><strong><?= $money($totalPrevisto) ?></strong><p>Valor total cadastrado.</p></div></div></div>
        <div class="col-lg-4 col-md-6"><div class="gc-metric"><span class="gc-metric-icon blue"><i class="fas fa-exchange-alt"></i></span><div><small>Total distribuído</small><strong><?= $money(array_sum(array_column($porUniversidade, 'valor_previsto'))) ?></strong><p>Distribuição entre universidades.</p></div></div></div>
        <div class="col-lg-4 col-md-6"><div class="gc-metric"><span class="gc-metric-icon purple"><i class="fas fa-th-large"></i></span><div><small>Categorias</small><strong><?= count($categorias) ?></strong><p>Categorias de custos cadastradas.</p></div></div></div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <section class="gc-panel">
                <header><i class="fas fa-layer-group"></i><div><h2>Custos por categoria</h2><p>Planejamento orçamentário por natureza do custo.</p></div></header>
                <div class="gc-panel-body table-responsive">
                    <table class="table table-hover gc-table">
                        <thead><tr><th>Categoria</th><th>Edição</th><th>Previsto</th><th>Distribuído</th><th>Saldo a distribuir</th><th>Ações</th></tr></thead>
                        <tbody>
                        <?php if (!$categorias): ?><tr><td colspan="6" class="gc-empty">Nenhum custo cadastrado para o filtro selecionado.</td></tr><?php endif; ?>
                        <?php foreach ($categorias as $categoria): ?>
                            <?php $distribuido = array_sum(array_map(static fn($item) => (float) $item->valor_previsto, $categoria->distribuicoes)); ?>
                            <tr>
                                <td><strong><?= Html::encode($categoria->tipoCategoriaCusto ? $categoria->tipoCategoriaCusto->nome : '-') ?></strong><small><?= Html::encode($categoria->descricao) ?></small></td>
                                <td><?= Html::encode($categoria->trote ? $categoria->trote->edicao : '-') ?></td>
                                <td><?= $money($categoria->valor_previsto) ?></td>
                                <td><?= $money($distribuido) ?></td>
                                <td><?= $money((float) $categoria->valor_previsto - $distribuido) ?></td>
                                <td class="gc-actions">
                                    <?= Html::a('<i class="fas fa-pencil-alt"></i>', ['update', 'id' => $categoria->id], ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'Editar', 'aria-label' => 'Editar custo']) ?>
                                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $categoria->id], ['class' => 'btn btn-sm btn-outline-danger', 'title' => 'Excluir', 'aria-label' => 'Excluir custo', 'data' => ['method' => 'post', 'confirm' => 'Deseja excluir este custo e suas distribuições?']]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot><tr><th colspan="2">TOTAL</th><th><?= $money($totalPrevisto) ?></th><th><?= $money(array_sum(array_column($porUniversidade, 'valor_previsto'))) ?></th><th colspan="2"></th></tr></tfoot>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="gc-panel">
                <header><i class="fas fa-map-marker-alt"></i><div><h2>Por universidade / cidade</h2><p>Valores previstos distribuídos.</p></div></header>
                <div class="gc-panel-body table-responsive">
                    <table class="table gc-table"><thead><tr><th>Universidade / cidade</th><th>Previsto</th></tr></thead><tbody>
                    <?php if (!$porUniversidade): ?><tr><td colspan="2" class="gc-empty">Nenhuma distribuição cadastrada.</td></tr><?php endif; ?>
                    <?php foreach ($porUniversidade as $item): ?><tr><td><?= Html::encode($item['universidade'] ? $item['universidade']->nome . ' — ' . $item['universidade']->cidade : '-') ?></td><td><?= $money($item['valor_previsto']) ?></td></tr><?php endforeach; ?>
                    </tbody></table>
                </div>
            </section>
        </div>
    </div>

    <section class="gc-panel">
        <header><i class="fas fa-chart-bar"></i><div><h2>Distribuição dos custos</h2><p>Comparativo entre categorias planejadas.</p></div></header>
        <div class="gc-panel-body gc-bars">
            <?php if (!$categorias): ?><div class="gc-empty">Cadastre o primeiro custo para visualizar o comparativo.</div><?php endif; ?>
            <?php foreach ($categorias as $categoria): ?>
                <div class="gc-bar-row"><span><?= Html::encode($categoria->tipoCategoriaCusto ? $categoria->tipoCategoriaCusto->nome : '-') ?></span><div><i style="width: <?= $maxValue > 0 ? round(((float) $categoria->valor_previsto / $maxValue) * 100, 2) : 0 ?>%"></i></div><strong><?= $money($categoria->valor_previsto) ?></strong></div>
            <?php endforeach; ?>
        </div>
    </section>
</div>
