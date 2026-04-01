<?php

use yii\helpers\Html;

$this->title = 'Minha area';

$totalParticipacoes = count($participacoes);
$totalParticipacoesAtivas = count($participacoesAtivas);
$totalDoacoes = count($doacoes);
$totalCertificados = count($certificados);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Minha area</h2>
            <span class="text-muted">Acompanhe sua participacao no Trote Solidario</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <?= Html::a('Meu perfil', ['/participante/users/perfil'], ['class' => 'btn btn-outline-dark']) ?>
            <?= Html::a('Nova doacao', ['/participante/doacao/create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php if ($banner !== null && (!empty($banner->img_dsk) || !empty($banner->img_mob))): ?>
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <img src="/img/<?= Html::encode(\app\modules\common\models\Helper::isMobile() ? ($banner->img_mob ?: $banner->img_dsk) : ($banner->img_dsk ?: $banner->img_mob)) ?>" style="width:100%;height:auto;" alt="Banner do trote">
        </div>
    <?php endif; ?>

    <?php if ($troteAtivo !== null): ?>
        <div class="card shadow-sm border-0 mb-4 border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Trote ativo</h5>
                    <span class="badge bg-success"><?= Html::encode($troteAtivo->getStatusLabel()) ?></span>
                </div>
                <h4 class="fw-bold mb-1"><?= Html::encode($troteAtivo->titulo ?: ('Trote ' . $troteAtivo->edicao)) ?></h4>
                <div class="text-muted">Edicao <?= Html::encode($troteAtivo->edicao) ?></div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <small class="text-muted">Inicio</small>
                        <div class="fw-semibold"><?= $troteAtivo->data_inicio ? Yii::$app->formatter->asDate($troteAtivo->data_inicio, 'php:d/m/Y') : '-' ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Fim</small>
                        <div class="fw-semibold"><?= $troteAtivo->data_fim ? Yii::$app->formatter->asDate($troteAtivo->data_fim, 'php:d/m/Y') : '-' ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Minhas participacoes ativas</small>
                        <div class="fw-semibold"><?= $totalParticipacoesAtivas ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Meu ranking universitario</small>
                        <div class="fw-semibold"><?= !empty($ranking) ? 'Atualizado' : 'Sem dados' ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ([
            ['label' => 'Participacoes', 'value' => $totalParticipacoes, 'color' => 'primary'],
            ['label' => 'Participacoes ativas', 'value' => $totalParticipacoesAtivas, 'color' => 'success'],
            ['label' => 'Doacoes', 'value' => $totalDoacoes, 'color' => 'warning'],
            ['label' => 'Certificados', 'value' => $totalCertificados, 'color' => 'info'],
        ] as $card): ?>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-1"><?= Html::encode($card['label']) ?></div>
                        <div class="display-6 fw-bold text-<?= Html::encode($card['color']) ?>"><?= (int) $card['value'] ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Minhas participacoes</h5>
                        <?= Html::a('Ver doacoes', ['/participante/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <?php if (!empty($participacoes)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($participacoes as $participacao): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold"><?= Html::encode($participacao->universidade->nome ?? '-') ?></div>
                                        <small class="text-muted"><?= Html::encode(($participacao->trote->titulo ?: 'Trote') . ' | ' . ($participacao->trote->edicao ?? '-')) ?></small>
                                    </div>
                                    <span class="badge <?= $participacao->status === \app\modules\common\models\Participacao::STATUS_ATIVO ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= Html::encode(\app\modules\common\models\Participacao::getStatusList()[$participacao->status] ?? $participacao->status) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Voce ainda nao possui participacoes cadastradas.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Ranking por universidade</h5>
                        <?= Html::a('Ranking completo', ['/participante/default/ranking'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <?php if (!empty($ranking)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Universidade</th>
                                        <th>Pontos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ranking as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= Html::encode($item['nome']) ?></td>
                                            <td><strong><?= (int) $item['pontos'] ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">O ranking ainda nao possui dados suficientes para exibicao.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Informativos</h5>
                    <?php if (!empty($informativos)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($informativos as $informativo): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span><?= Html::encode($informativo->nome ?? ('Documento #' . $informativo->id)) ?></span>
                                    <?php if (!empty($informativo->arquivo)): ?>
                                        <a href="/pdf/<?= Html::encode($informativo->arquivo) ?>" target="_blank" class="btn btn-outline-success btn-sm">Abrir</a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Nenhum informativo disponivel no momento.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Regulamentos</h5>
                    <?php if (!empty($regulamentos)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($regulamentos as $regulamento): ?>
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span><?= Html::encode($regulamento->nome ?? ('Regulamento #' . $regulamento->id)) ?></span>
                                    <?php if (!empty($regulamento->arquivo)): ?>
                                        <a href="/pdf/<?= Html::encode($regulamento->arquivo) ?>" target="_blank" class="btn btn-outline-success btn-sm">Abrir</a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Nenhum regulamento disponivel no momento.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
