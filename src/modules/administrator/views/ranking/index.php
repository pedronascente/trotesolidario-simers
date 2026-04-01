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
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <div>
                            <h4 class="mb-1">Relatorio de ranking</h4>
                            <div class="text-muted">Posicoes por participacao e consolidado por universidade.</div>
                        </div>
                        <div class="btn-group-actions">
                            <?= Html::beginForm(['rebuild'], 'post') ?>
                            <?= Html::dropDownList('trote_id', $selectedTroteId, $trotes, ['prompt' => 'Todos os trotes', 'class' => 'form-control']) ?>
                            <?= Html::submitButton('Recalcular ranking', ['class' => 'btn btn-success']) ?>
                            <?= Html::endForm() ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                            <td><?= Html::encode($item['nome']) ?></td>
                                            <td><strong><?= (int) $item['pontos'] ?></strong></td>
                                            <td><?= (int) $item['participantes'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-muted">Nenhum ranking consolidado encontrado para o filtro atual.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
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
                            ],
                            [
                                'attribute' => 'posicao',
                                'label' => 'Posicao',
                            ],
                            [
                                'attribute' => 'updated_at',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                                'filter' => false,
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
