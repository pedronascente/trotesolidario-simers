<?php

use app\modules\common\models\Helper;
use app\assets\ParticipantDashboardAsset;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Visão geral';
ParticipantDashboardAsset::register($this);

$troteAtivoGlobalDisplayEdition = $troteAtivoGlobal !== null ? str_replace('.', '/', (string) $troteAtivoGlobal->edicao) : '-';
$hasMultipleTrotes = !empty($trotesDisponiveis) && count($trotesDisponiveis) > 1;
$rankingMaxPoints = 0;
foreach ($ranking as $rankingItem) {
    $rankingMaxPoints = max($rankingMaxPoints, (int) ($rankingItem['pontos'] ?? 0));
}

$summaryCards = [
    [
        'label' => 'Doações',
        'value' => $dashboardSummary['doacoes'],
        'hint' => 'Registros enviados',
        'icon' => 'fas fa-hand-holding-heart',
        'variant' => 'primary',
    ],
    [
        'label' => 'Aprovadas',
        'value' => $dashboardSummary['doacoesAprovadas'],
        'hint' => 'Doações validadas',
        'icon' => 'fas fa-check',
        'variant' => 'success',
    ],
    [
        'label' => 'Pendentes',
        'value' => $dashboardSummary['doacoesPendentes'],
        'hint' => 'Aguardando validação',
        'icon' => 'fas fa-clock',
        'variant' => 'warning',
    ],
    [
        'label' => 'Certificados',
        'value' => $dashboardSummary['certificados'],
        'hint' => 'Disponíveis para acesso',
        'icon' => 'fas fa-file-contract',
        'variant' => 'info',
    ],
];
?>

<div class="participant-dashboard container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= Html::encode(Yii::$app->session->getFlash('success')) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= Html::encode(Yii::$app->session->getFlash('error')) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <section class="dashboard-hero mb-4">
        <span class="dashboard-hero-shape dashboard-hero-shape-one" aria-hidden="true"></span>
        <span class="dashboard-hero-shape dashboard-hero-shape-two" aria-hidden="true"></span>
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="dashboard-eyebrow"><i class="fas fa-graduation-cap mr-2" aria-hidden="true"></i>Minha jornada universitária</div>
                <h1 class="mb-2">Bora transformar solidariedade em impacto?</h1>
                <p class="mb-0">
                    Acompanhe seu trote, registre doações e celebre cada conquista da sua universidade em um só lugar.
                </p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0">
                <div class="dashboard-actions d-flex flex-wrap justify-content-lg-end">
                    <?php if ($troteAtivoGlobal !== null): ?>
                        <?= Html::a(
                            '<i class="fas fa-plus-circle mr-2" aria-hidden="true"></i><span>Registrar doação</span>',
                            ['/participante/doacao/create'],
                            ['class' => 'btn dashboard-action-primary mb-2']
                        ) ?>
                    <?php endif; ?>

                    <?= Html::a(
                        '<i class="fas fa-apple-alt mr-2" aria-hidden="true"></i><span>Doar alimentos</span>',
                        '#doacao-alimentos',
                        ['class' => 'btn dashboard-action-food mb-2']
                    ) ?>

                </div>
            </div>
        </div>
    </section>

    <?php if ($banner !== null && (!empty($banner->img_dsk) || !empty($banner->img_mob))): ?>
        <div class="dashboard-banner card shadow-sm border-0 mb-4 overflow-hidden">
            <img
                src="<?= Html::encode(Url::to('@web/img/' . rawurlencode(basename(Helper::isMobile() ? ($banner->img_mob ?: $banner->img_dsk) : ($banner->img_dsk ?: $banner->img_mob))))) ?>"
                alt="Banner do trote"
            >
        </div>
    <?php endif; ?>

    <?php if ($showStartParticipationCard && $troteAtivoGlobal !== null): ?>
        <section class="card start-participation-card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="dashboard-eyebrow dashboard-eyebrow-light">Trote ativo disponível</div>
                        <h2 class="h4 mb-2 text-white">Comece a participar do Trote Solidário <?= Html::encode($troteAtivoGlobalDisplayEdition) ?></h2>
                        <p class="mb-0">
                            Informe sua universidade e seu curso para iniciar a participação.
                        </p>
                    </div>
                    <div class="col-lg-4 mt-3 mt-lg-0 text-lg-right">
                        <button type="button" class="btn btn-light text-success font-weight-bold" data-toggle="modal" data-target="#startParticipationModal">
                            Participar agora
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <?= $this->render('_start_participation_modal', [
            'startParticipationModel' => $startParticipationModel,
            'startParticipationUniversidades' => $startParticipationUniversidades,
            'troteAtivoGlobal' => $troteAtivoGlobal,
            'troteAtivoGlobalDisplayEdition' => $troteAtivoGlobalDisplayEdition,
        ]) ?>
    <?php endif; ?>

    <section class="card dashboard-context-card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="<?= $hasMultipleTrotes ? 'col-lg-8' : 'col-12' ?>">
                    <div class="dashboard-eyebrow text-success">Trote ativo</div>
                    <?php if ($troteAtivo !== null): ?>
                        <div class="d-flex align-items-center flex-wrap mb-2">
                            <h2 class="h4 mb-0 mr-2 text-gray-900"><?= Html::encode($troteAtivo->titulo ?: ('Trote ' . $troteAtivo->edicao)) ?></h2>
                            <span class="badge badge-success"><?= Html::encode($troteAtivo->getStatusLabel()) ?></span>
                        </div>
                        <p class="mb-3 text-muted">Edição <?= Html::encode($troteAtivo->edicao) ?></p>
                        <div class="row dashboard-context-metrics">
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                <span>Início</span>
                                <strong><?= $troteAtivo->data_inicio ? Yii::$app->formatter->asDate($troteAtivo->data_inicio, 'php:d/m/Y') : '-' ?></strong>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                <span>Encerramento</span>
                                <strong><?= $troteAtivo->data_fim ? Yii::$app->formatter->asDate($troteAtivo->data_fim, 'php:d/m/Y') : '-' ?></strong>
                            </div>
                            <div class="col-sm-4">
                                <span>Ranking</span>
                                <?= Html::a(
                                    !empty($ranking) ? 'Ver ranking <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>' : 'Acompanhar ranking',
                                    ['/participante/default/ranking', 'trote_id' => $selectedTroteId],
                                    ['class' => 'dashboard-context-link']
                                ) ?>
                            </div>
                        </div>
                    <?php elseif ($showStartParticipationCard && $troteAtivoGlobal !== null): ?>
                        <h2 class="h4 mb-2 text-gray-900"><?= Html::encode($troteAtivoGlobal->titulo ?: ('Trote ' . $troteAtivoGlobalDisplayEdition)) ?></h2>
                        <p class="mb-0 text-muted">Você ainda não iniciou sua participação neste trote ativo.</p>
                    <?php else: ?>
                        <h2 class="h4 mb-2 text-gray-900">Aguardando o próximo trote</h2>
                        <p class="mb-0 text-muted">Assim que uma edição estiver disponível, você poderá acompanhar aqui datas, ranking e atividades.</p>
                    <?php endif; ?>
                </div>

                <?php if ($hasMultipleTrotes): ?>
                <div class="col-lg-4 mt-4 mt-lg-0">
                        <form method="get" action="<?= Html::encode(Url::to(['/participante/default/home'])) ?>" class="dashboard-trote-filter">
                            <label for="dashboard-trote-id">Edição selecionada</label>
                            <select id="dashboard-trote-id" name="trote_id" class="form-control" onchange="this.form.submit()">
                                <?php foreach ($trotesDisponiveis as $troteOption): ?>
                                    <option value="<?= (int) $troteOption->id ?>" <?= $selectedTroteId === (int) $troteOption->id ? 'selected' : '' ?>>
                                        <?= Html::encode(($troteOption->titulo ?: 'Trote') . ' | ' . ($troteOption->edicao ?? '-')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small>Os dados do painel serão atualizados automaticamente.</small>
                        </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="row dashboard-summary-row">
        <?php foreach ($summaryCards as $card): ?>
            <div class="col-sm-6 col-xl-3 mb-4">
                <div class="card dashboard-summary-card dashboard-summary-<?= Html::encode($card['variant']) ?> shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="dashboard-summary-label"><?= Html::encode($card['label']) ?></div>
                                <div class="dashboard-summary-value"><?= (int) $card['value'] ?></div>
                            </div>
                            <div class="dashboard-summary-icon">
                                <i class="<?= Html::encode($card['icon']) ?>" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="dashboard-summary-hint"><?= Html::encode($card['hint']) ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <div class="row">
        <!-- Informativos -->
        <div class="col-lg-6 mb-4">
            <section class="card dashboard-food-card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="dashboard-section-header">
                        <h2 class="h5 mb-0 text-gray-900">Informativos</h2>
                    </div>

                    <p>
                        Para a doação de sangue, o participante deve ir ao hemocentro (mediante agendamento prévio), em horários alternativos, evitando aglomerações de 
                        pessoas...
                    </p>

                    <?php if (!empty($informativos)): ?>
                        <ul class="list-group list-group-flush dashboard-list">
                            <?php foreach ($informativos as $informativo): ?>
                                <li class="list-group-item dashboard-list-item d-flex justify-content-between align-items-center">
                                    <span class="dashboard-document-name">
                                        <?= Html::encode($informativo->nome ?? ('Documento #' . $informativo->id)) ?>
                                    </span>
                                    <a href="<?= Html::encode(Url::to('@web/pdf/' . rawurlencode(basename($informativo->arquivo)))) ?>" target="_blank"
                                    class="btn btn-outline-success btn-sm" rel="noopener noreferrer">
                                    Abrir
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="dashboard-empty-state">Nenhum informativo disponível no momento.</div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <!-- Regulamentos -->
        <div class="col-lg-6 mb-4">
            <section class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="dashboard-section-header">
                        <h2 class="h5 mb-0 text-gray-900">Regulamentos</h2>
                    </div>
                    <?php if (!empty($regulamentos)): ?>
                        <ul class="list-group list-group-flush dashboard-list">
                            <?php foreach ($regulamentos as $regulamento): ?>
                                <li class="list-group-item dashboard-list-item d-flex justify-content-between align-items-center">
                                    <span class="dashboard-document-name">
                                        <?= Html::encode($regulamento->nome ?? ('Regulamento #' . $regulamento->id)) ?>
                                    </span>
                                    <a href="<?= Html::encode(Url::to('@web/pdf/' . rawurlencode(basename($regulamento->arquivo)))) ?>" target="_blank"
                                    class="btn btn-outline-success btn-sm" rel="noopener noreferrer">
                                    Abrir
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="dashboard-empty-state">Nenhum regulamento disponível no momento.</div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

    <?= $this->render('_home_tracking', [
        'participacoes' => $participacoes,
        'selectedTroteId' => $selectedTroteId,
        'hasMultipleTrotes' => $hasMultipleTrotes,
        'ranking' => $ranking,
        'rankingMaxPoints' => $rankingMaxPoints,
    ]) ?>

    <section id="doacao-alimentos" class="card dashboard-food-card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="dashboard-eyebrow text-success">Doação de alimentos</div>
                    <h2 class="h4 mb-2 text-gray-900">Escolha uma universidade e doe alimentos online.</h2>
                    <p class="mb-0 text-muted">
                        Cada link direciona para a campanha da universidade escolhida. Selecione os itens, conclua a doação e ajude instituições da cidade-sede.
                    </p>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0 text-lg-right">
                    <span class="dashboard-food-count"><?= count($universidadesDoacao) ?> universidade(s) disponível(is)</span>
                </div>
            </div>

            <?php if (!empty($universidadesDoacao)): ?>
                <div class="row mt-4">
                    <?php foreach ($universidadesDoacao as $universidade): ?>
                        <div class="col-sm-6 col-lg-4 col-xl-3 mb-3">
                            <?= Html::a(
                                '<span>' . Html::encode($universidade->nome) . '</span><i class="fas fa-external-link-alt ml-2" aria-hidden="true"></i>',
                                $universidade->link_doacao_alimento,
                                [
                                    'class' => 'dashboard-food-link',
                                    'target' => '_blank',
                                    'rel' => 'noopener noreferrer',
                                    'aria-label' => 'Doar alimentos para ' . $universidade->nome,
                                ]
                            ) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="dashboard-empty-state mt-4">
                    Links de doação de alimentos ainda não estão disponíveis.
                </div>
            <?php endif; ?>
        </div>
    </section>

</div>

<?php
if ($showStartParticipationCard && $troteAtivoGlobal !== null && $shouldOpenStartParticipationModal) {
    $this->registerJs("jQuery('#startParticipationModal').modal('show');");
}
?>
