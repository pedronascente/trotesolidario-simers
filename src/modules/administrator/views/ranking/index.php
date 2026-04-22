<?php

use app\modules\common\models\Trote;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Ranking';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 8px; align-items: center; }
    .ranking-toolbar { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
    .ranking-toolbar-form { display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap; margin: 0; }
    .ranking-toolbar-field { min-width: 220px; }
    .ranking-toolbar-label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #5a5c69;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .ranking-summary-card { border-left: 4px solid #1cc88a; }
    .ranking-summary-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .ranking-summary-meta { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 12px; }
    .ranking-summary-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f8f9fc;
        color: #5a5c69;
        font-size: 12px;
        font-weight: 600;
    }
    .ranking-table-note {
        margin-top: 12px;
        font-size: 12px;
        color: #858796;
    }
    .ranking-position-badge {
        display: inline-flex;
        min-width: 32px;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: #eefaf4;
        color: #169b62;
        font-weight: 700;
    }
    .ranking-points {
        font-weight: 700;
        color: #2e2f37;
    }
</style>

<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Ranking', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Ranking', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 ranking-summary-card">
                <div class="card-body">
                    <div class="ranking-summary-header mb-3">
                        <div>
                            <h4 class="mb-1">Relatorio de ranking</h4>
                            <div class="text-muted">Resumo consolidado por universidade com base no cache atual do ranking.</div>
                            <div class="ranking-summary-meta">
                                <span class="ranking-summary-chip">Filtro: <?= Html::encode($selectedTroteId !== null ? ($trotes[$selectedTroteId] ?? 'Trote selecionado') : 'Todos os trotes') ?></span>
                                <span class="ranking-summary-chip">Universidades listadas: <?= count($universityRanking) ?></span>
                            </div>
                        </div>
                        <div class="ranking-toolbar">
                            <?= Html::beginForm(['index'], 'get', ['class' => 'ranking-toolbar-form']) ?>
                                <div class="ranking-toolbar-field">
                                    <label class="ranking-toolbar-label" for="ranking-filter-trote">Filtro do relatorio</label>
                                    <?= Html::dropDownList('trote_id', $selectedTroteId, $trotes, ['prompt' => 'Todos os trotes', 'class' => 'form-control', 'id' => 'ranking-filter-trote']) ?>
                                </div>
                                <?= Html::submitButton('Filtrar', ['class' => 'btn btn-outline-secondary']) ?>
                                <?php if ($selectedTroteId !== null): ?>
                                    <?= Html::a('Limpar', ['index'], ['class' => 'btn btn-link']) ?>
                                <?php endif; ?>
                            <?= Html::endForm() ?>

                            <?= Html::beginForm(['rebuild'], 'post', ['class' => 'ranking-toolbar-form']) ?>
                                <?= Html::hiddenInput('trote_id', $selectedTroteId) ?>
                                <?= Html::submitButton(
                                    $selectedTroteId !== null ? 'Recalcular trote filtrado' : 'Recalcular todos',
                                    ['class' => 'btn btn-success']
                                ) ?>
                            <?= Html::endForm() ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <?php if ($selectedTroteId === null): ?>
                                        <th>Trote</th>
                                    <?php endif; ?>
                                    <th>Universidade</th>
                                    <th>Pontos</th>
                                    <th>Participacoes ranqueadas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($universityRanking)): ?>
                                    <?php foreach ($universityRanking as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <?php if ($selectedTroteId === null): ?>
                                                <td><?= Html::encode($trotes[(int) ($item['trote_id'] ?? 0)] ?? ('Trote #' . (int) ($item['trote_id'] ?? 0))) ?></td>
                                            <?php endif; ?>
                                            <td><?= Html::encode($item['nome']) ?></td>
                                            <td><span class="ranking-points"><?= (int) $item['pontos'] ?></span></td>
                                            <td><?= (int) $item['participantes'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="<?= $selectedTroteId === null ? 5 : 4 ?>" class="text-muted">Nenhum ranking consolidado encontrado para o filtro atual.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($selectedTroteId === null && !empty($universityRanking)): ?>
                        <div class="ranking-table-note">
                            Sem filtro de trote, o relatorio agrupa universidades por edicao. A coluna <strong>Trote</strong> indica a qual ranking cada linha pertence.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-trophy"></i> Ranking por participacao',
                            'before' => '<div class="text-muted small" style="padding-top: 7px;">Cada linha representa uma participacao consolidada no cache do ranking.</div>',
                        ],
                        'export' => ['fontAwesome' => true],
                        'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],
                        'columns' => [
                            'id',
                            [
                                'attribute' => 'trote_edicao',
                                'label' => 'Trote',
                                'value' => static fn($model) => $model->trote ? (($model->trote->titulo ?: 'Sem titulo') . ' | ' . ($model->trote->edicao ?: '-')) : '-',
                                'filter' => ArrayHelper::map(Trote::find()->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])->all(), 'edicao', 'edicao'),
                            ],
                            [
                                'attribute' => 'user_nome',
                                'label' => 'Participante',
                                'value' => static fn($model) => $model->participacao->user->nome ?? '-',
                            ],
                            [
                                'attribute' => 'universidade_nome',
                                'label' => 'Universidade',
                                'value' => static fn($model) => $model->participacao->universidade->nome ?? '-',
                            ],
                            [
                                'attribute' => 'pontuacao_total',
                                'label' => 'Pontos',
                                'contentOptions' => ['class' => 'text-nowrap'],
                                'value' => static fn($model) => (int) $model->pontuacao_total,
                            ],
                            [
                                'attribute' => 'posicao',
                                'label' => 'Posicao',
                                'format' => 'raw',
                                'value' => static fn($model) => '<span class="ranking-position-badge">' . (int) $model->posicao . '</span>',
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                                'filter' => false,
                            ],
                        ],
                    ]) ?>
                    <div class="ranking-table-note">
                        O campo <strong>ID</strong> identifica o registro do cache. A classificacao da participacao esta na coluna <strong>Posicao</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
