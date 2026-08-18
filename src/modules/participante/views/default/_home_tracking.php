<?php

use app\modules\common\models\Participacao;
use yii\helpers\Html;

?>

<div class="row">
    <div class="col-xl-8">
        <section class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="dashboard-section-header">
                    <div>
                        <div class="dashboard-eyebrow text-success">Acompanhamento</div>
                        <h2 class="h5 mb-0 text-gray-900">Minhas participações</h2>
                    </div>
                    <?= Html::a('Ver doações', ['/participante/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                </div>

                <?php if (!empty($participacoes)): ?>
                    <ul class="list-group list-group-flush dashboard-list">
                        <?php foreach ($participacoes as $participacao): ?>
                            <?php
                            $trote = $participacao->trote;
                            $troteLabel = $trote !== null
                                ? (($trote->titulo ?: 'Trote') . ' | ' . ($trote->edicao ?? '-'))
                                : 'Trote não encontrado';
                            $statusClass = $participacao->status === Participacao::STATUS_ATIVO ? 'badge-success' : 'badge-secondary';
                            ?>
                            <li class="list-group-item dashboard-list-item">
                                <div class="dashboard-list-main">
                                    <div class="font-weight-bold text-gray-900"><?= Html::encode($participacao->universidade->nome ?? '-') ?></div>
                                    <small class="text-muted"><?= Html::encode($troteLabel) ?></small>
                                </div>
                                <span class="badge <?= Html::encode($statusClass) ?>">
                                    <?= Html::encode(Participacao::getStatusList()[$participacao->status] ?? $participacao->status) ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="dashboard-empty-state">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        <span>Você ainda não possui participações cadastradas.</span>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="dashboard-section-header">
                    <div>
                        <div class="dashboard-eyebrow text-success">Top universidades</div>
                        <h2 class="h5 mb-0 text-gray-900">Ranking</h2>
                    </div>
                    <?= Html::a('Completo', ['/participante/default/ranking', 'trote_id' => $selectedTroteId], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                </div>

                <?php if ($selectedTroteId === null && $hasMultipleTrotes): ?>
                    <div class="dashboard-empty-state">Selecione um trote para visualizar o ranking correspondente.</div>
                <?php elseif (!empty($ranking)): ?>
                    <div class="dashboard-ranking-list">
                        <?php foreach ($ranking as $index => $item): ?>
                            <?php
                            $points = (int) ($item['pontos'] ?? 0);
                            $percent = $rankingMaxPoints > 0 ? min(100, (int) round(($points / $rankingMaxPoints) * 100)) : 0;
                            ?>
                            <div class="dashboard-ranking-item">
                                <div class="dashboard-ranking-position"><?= $index + 1 ?></div>
                                <div class="dashboard-ranking-body">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= Html::encode($item['nome'] ?? '-') ?></strong>
                                        <span><?= $points ?> pts</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percent ?>%;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="dashboard-empty-state">
                        <i class="fas fa-chart-line" aria-hidden="true"></i>
                        <span>O ranking será exibido quando houver dados suficientes.</span>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
