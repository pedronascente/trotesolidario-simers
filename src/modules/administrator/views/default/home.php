<?php

use app\modules\common\models\Doacao;
use yii\helpers\Html;

$this->title = 'Dashboard';

$cards = [
    ['label' => 'Usuarios', 'value' => $totalUsuarios, 'color' => 'primary', 'icon' => 'bi-people'],
    ['label' => 'Participantes', 'value' => $totalParticipantes, 'color' => 'success', 'icon' => 'bi-person-check'],
    ['label' => 'Doacoes', 'value' => $totalDoacoes, 'color' => 'warning', 'icon' => 'bi-cash'],
    ['label' => 'Eventos', 'value' => $totalEventos, 'color' => 'info', 'icon' => 'bi-calendar-event'],
];

$relatorioDoacoes = [
    ['label' => 'Pendentes', 'value' => $doacoesPendentes, 'class' => 'warning'],
    ['label' => 'Aprovadas', 'value' => $doacoesAprovadas, 'class' => 'success'],
    ['label' => 'Rejeitadas', 'value' => $doacoesRejeitadas, 'class' => 'danger'],
];
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Dashboard</h2>
            <span class="text-muted">Relatorios operacionais e acompanhamento do sistema</span>
        </div>
        <div class="text-muted small">
            Pagina renderizada em <?= Yii::$app->formatter->asDatetime('now', 'php:d/m/Y H:i') ?>
        </div>
    </div>

    <?php if ($troteResumo !== null): ?>
        <?php $ativoNoPeriodo = (!empty($troteResumo['data_inicio']) && !empty($troteResumo['data_fim']) && date('Y-m-d') >= $troteResumo['data_inicio'] && date('Y-m-d') <= $troteResumo['data_fim']); ?>
        <div class="card shadow-sm border-0 mb-4 border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Trote ativo</h5>
                    <span class="badge bg-success"><?= Html::encode($troteResumo['status']) ?></span>
                </div>

                <h4 class="fw-bold mb-1"><?= Html::encode($troteResumo['nome']) ?></h4>
                <div class="text-muted">Edicao <?= Html::encode($troteResumo['edicao']) ?></div>

                <?php if (!empty($trotesAtivosResumo) && count($trotesAtivosResumo) > 1): ?>
                    <div class="alert alert-warning py-2 px-3 mt-3 mb-0">
                        O dashboard esta exibindo o trote ativo mais recente. Existem <?= count($trotesAtivosResumo) ?> trotes com status ativo no momento:
                        <?php
                            $trotesRotulos = array_map(static function (array $trote): string {
                                return $trote['nome'] . ' | ' . $trote['edicao'];
                            }, $trotesAtivosResumo);
                        ?>
                        <strong><?= Html::encode(implode(' | ', $trotesRotulos)) ?></strong>
                    </div>
                <?php endif; ?>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <small class="text-muted">Data inicio</small>
                        <div class="fw-semibold"><?= $troteResumo['data_inicio'] ? Yii::$app->formatter->asDate($troteResumo['data_inicio'], 'php:d/m/Y') : '-' ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Data fim</small>
                        <div class="fw-semibold"><?= $troteResumo['data_fim'] ? Yii::$app->formatter->asDate($troteResumo['data_fim'], 'php:d/m/Y') : '-' ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Primeiro evento vinculado</small>
                        <div class="fw-semibold"><?= Html::encode($troteResumo['evento'] ?: 'Sem evento vinculado') ?></div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Participacoes ativas</small>
                        <div class="fw-bold fs-5 text-primary"><?= (int) $troteResumo['total_participantes'] ?></div>
                    </div>
                </div>

                <div class="mt-3">
                    <?php if ($ativoNoPeriodo): ?>
                        <span class="text-success fw-semibold">Dentro do periodo ativo</span>
                    <?php else: ?>
                        <span class="text-danger fw-semibold">Trote ativo fora da janela de datas configurada</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mb-4">
            Nenhum trote com status ativo foi encontrado para compor o relatorio principal.
        </div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($cards as $card): ?>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1"><?= Html::encode($card['label']) ?></h6>
                            <h3 class="fw-bold mb-0"><?= (int) $card['value'] ?></h3>
                        </div>
                        <i class="bi <?= Html::encode($card['icon']) ?> fs-1 text-<?= Html::encode($card['color']) ?>"></i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Relatorio de doacoes</h5>
                        <?= Html::a('Abrir modulo', ['/administrator/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <div class="row">
                        <?php foreach ($relatorioDoacoes as $item): ?>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <div class="text-muted small text-uppercase"><?= Html::encode($item['label']) ?></div>
                                    <div class="display-6 fw-bold text-<?= Html::encode($item['class']) ?>"><?= (int) $item['value'] ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="small text-muted">
                        O acompanhamento usa os status atuais do modulo de doacao: <?= implode(', ', array_values(Doacao::getStatusList())) ?>.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Acoes rapidas</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= Html::a('Novo evento', ['/administrator/evento/create'], ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Novo trote', ['/administrator/trote/create'], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Usuarios', ['/administrator/user'], ['class' => 'btn btn-outline-dark']) ?>
                        <?= Html::a('Doacoes', ['/administrator/doacao'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('Participacoes', ['/administrator/participacao'], ['class' => 'btn btn-outline-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Universidades em destaque</h5>
                        <?= Html::a('Ver participacoes', ['/administrator/participacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <?php if (!empty($rankingUniversidades)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Universidade</th>
                                        <th>Pontos</th>
                                        <th>Participacoes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rankingUniversidades as $index => $universidade): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= Html::encode($universidade['nome']) ?></td>
                                            <td><strong><?= (int) $universidade['pontos'] ?></strong></td>
                                            <td><?= (int) $universidade['participantes'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">Ainda nao ha dados suficientes para montar o ranking de universidades.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Ultimos usuarios</h5>
                        <?= Html::a('Gerenciar usuarios', ['/administrator/user'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <?php if (!empty($ultimosUsuarios)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($ultimosUsuarios as $user): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <div class="fw-semibold"><?= Html::encode($user->nome ?: $user->email) ?></div>
                                        <small class="text-muted"><?= Html::encode($user->email) ?></small>
                                    </div>
                                    <small class="text-muted"><?= $user->created_at ? Yii::$app->formatter->asDate($user->created_at, 'php:d/m/Y') : '-' ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Nenhum usuario cadastrado ainda.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Ultimas doacoes</h5>
                <?= Html::a('Abrir doacoes', ['/administrator/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            </div>

            <?php if (!empty($ultimasDoacoes)): ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Participante</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Criado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasDoacoes as $doacao): ?>
                                <tr>
                                    <td><?= Html::encode($doacao->participacao->user->nome ?? '-') ?></td>
                                    <td><?= Html::encode($doacao->tipoDoacao->nome ?? '-') ?></td>
                                    <td><?= Html::encode(Doacao::getStatusList()[$doacao->status] ?? $doacao->status) ?></td>
                                    <td><?= $doacao->created_at ? Yii::$app->formatter->asDatetime($doacao->created_at, 'php:d/m/Y H:i') : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted">Nenhuma doacao cadastrada ainda.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
