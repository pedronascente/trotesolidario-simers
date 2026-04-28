<?php

use app\modules\common\models\Doacao;
use app\modules\common\models\Helper;
use app\modules\common\models\Participacao;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Minha área';

$totalParticipacoes = count($participacoes);
$totalParticipacoesAtivas = count($participacoesAtivas);
$totalDoacoes = count($doacoes);
$totalCertificados = count($certificados);
$totalDoacoesAprovadas = count(array_filter($doacoes, static function (Doacao $doacao) {
    return $doacao->status === Doacao::STATUS_APROVADA;
}));
$totalDoacoesPendentes = count(array_filter($doacoes, static function (Doacao $doacao) {
    return $doacao->status === Doacao::STATUS_PENDENTE;
}));

$troteAtivoGlobalDisplayEdition = $troteAtivoGlobal !== null ? str_replace('.', '/', (string) $troteAtivoGlobal->edicao) : '-';
$hasMultipleTrotes = !empty($trotesDisponiveis) && count($trotesDisponiveis) > 1;
$rankingMaxPoints = 0;
foreach ($ranking as $rankingItem) {
    $rankingMaxPoints = max($rankingMaxPoints, (int) ($rankingItem['pontos'] ?? 0));
}

$summaryCards = [
    [
        'label' => 'Participações',
        'value' => $totalParticipacoes,
        'hint' => 'Historico completo',
        'icon' => 'fas fa-users',
        'variant' => 'primary',
    ],
    [
        'label' => 'Ativas',
        'value' => $totalParticipacoesAtivas,
        'hint' => 'Trote em andamento',
        'icon' => 'fas fa-running',
        'variant' => 'success',
    ],
    [
        'label' => 'Doacoes',
        'value' => $totalDoacoes,
        'hint' => $totalDoacoesPendentes . ' pendente(s)',
        'icon' => 'fas fa-hand-holding-heart',
        'variant' => 'warning',
    ],
    [
        'label' => 'Certificados',
        'value' => $totalCertificados,
        'hint' => 'Disponiveis para acesso',
        'icon' => 'fas fa-file-contract',
        'variant' => 'info',
    ],
];
?>

<style>

.participant-dashboard {
  background: linear-gradient(180deg, #f4fff8 0%, #f8fafc 45%, #ffffff 100%);
  padding-top: 24px;
  padding-bottom: 32px;
}

/* Hero */
.dashboard-hero {
  background: linear-gradient(135deg, #16a34a 0%, #22c55e 45%, #38bdf8 100%);
  border-radius: 24px;
  padding: 28px;
  color: #fff;
  box-shadow: 0 18px 45px rgba(22, 163, 74, 0.22);
}

.dashboard-hero h1,
.dashboard-hero p {
  color: #fff !important;
}

.dashboard-eyebrow {
  font-size: 0.76rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #16a34a;
  margin-bottom: 8px;
}

.dashboard-hero .dashboard-eyebrow,
.dashboard-eyebrow-light {
  color: rgba(255, 255, 255, 0.85);
}

/* Cards base */
.participant-dashboard .card {
  border-radius: 22px;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.participant-dashboard .card:hover {
  transform: translateY(-3px);
  box-shadow: 0 18px 35px rgba(15, 23, 42, 0.12) !important;
}

.participant-dashboard .card-body {
  padding: 24px;
}

/* Banner */
.dashboard-banner img {
  width: 100%;
  display: block;
  object-fit: cover;
}

/* Ações */
.dashboard-actions .btn {
  border-radius: 999px;
  padding: 10px 18px;
  font-weight: 700;
  transition: all 0.25s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.dashboard-action-primary {
  background: linear-gradient(135deg, #16a34a, #22c55e);
  color: #fff;
  border: none;
  box-shadow: 0 8px 18px rgba(34, 197, 94, 0.35);
}

.dashboard-action-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(34, 197, 94, 0.45);
  background: linear-gradient(135deg, #15803d, #16a34a);
}


/* Card iniciar participação */
.start-participation-card {
  background: linear-gradient(135deg, #059669 0%, #10b981 50%, #0ea5e9 100%);
  color: #fff;
}

.start-participation-card p {
  color: rgba(255, 255, 255, 0.9);
}

/* Contexto */
.dashboard-context-card {
  background: linear-gradient(135deg, #ffffff 0%, #ecfdf5 100%);
  border-left: 6px solid #22c55e !important;
}

.dashboard-context-metrics span {
  display: block;
  font-size: 0.75rem;
  color: #64748b;
  text-transform: uppercase;
  font-weight: 700;
}

.dashboard-context-metrics strong {
  display: block;
  color: #0f172a;
  font-size: 1rem;
}

/* Cards de resumo */
.dashboard-summary-card {
  position: relative;
  color: #fff;
  min-height: 150px;
}

.dashboard-summary-card::after {
  content: "";
  position: absolute;
  inset: auto -30px -35px auto;
  width: 120px;
  height: 120px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.18);
}

.dashboard-summary-primary {
  background: linear-gradient(135deg, #2563eb, #38bdf8);
}

.dashboard-summary-success {
  background: linear-gradient(135deg, #16a34a, #84cc16);
}

.dashboard-summary-warning {
  background: linear-gradient(135deg, #f59e0b, #f97316);
}

.dashboard-summary-info {
  background: linear-gradient(135deg, #7c3aed, #ec4899);
}

.dashboard-summary-label {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  font-weight: 800;
  opacity: 0.9;
}

.dashboard-summary-value {
  font-size: 2.4rem;
  font-weight: 900;
  line-height: 1;
  margin-top: 8px;
}

.dashboard-summary-hint {
  margin-top: 18px;
  font-size: 0.9rem;
  opacity: 0.92;
}

.dashboard-summary-icon {
  width: 52px;
  height: 52px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.22);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
}

/* Seções */
.dashboard-section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
}

.dashboard-food-card {
  background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
}

.dashboard-food-count {
  display: inline-block;
  background: #dcfce7;
  color: #166534;
  border-radius: 999px;
  padding: 8px 14px;
  font-weight: 800;
  font-size: 0.85rem;
}

/* Listas */
.dashboard-list-item {
  border: 0;
  border-radius: 16px !important;
  margin-bottom: 10px;
  background: #f8fafc;
  transition: background 0.2s ease, transform 0.2s ease;
}

.dashboard-list-item:hover {
  background: #ecfdf5;
  transform: translateX(4px);
}

.dashboard-document-name {
  font-weight: 700;
  color: #0f172a;
}

.dashboard-empty-state,
.dashboard-empty-note {
  background: #f1f5f9;
  color: #64748b;
  border-radius: 18px;
  padding: 18px;
  font-weight: 600;
}

/* Links de doação */
.dashboard-food-link {
  display: flex;
  justify-content: space-between;
  align-items: center;
  min-height: 58px;
  padding: 14px 16px;
  border-radius: 18px;
  background: linear-gradient(135deg, #dcfce7, #e0f2fe);
  color: #166534;
  font-weight: 800;
  text-decoration: none;
  box-shadow: 0 10px 22px rgba(34, 197, 94, 0.14);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.dashboard-food-link:hover {
  color: #14532d;
  text-decoration: none;
  transform: translateY(-3px);
  box-shadow: 0 16px 30px rgba(34, 197, 94, 0.22);
}

/* Ranking */
.dashboard-ranking-item {
  display: flex;
  gap: 14px;
  align-items: center;
  padding: 14px;
  border-radius: 18px;
  background: #f8fafc;
  margin-bottom: 12px;
}

.dashboard-ranking-position {
  width: 38px;
  height: 38px;
  border-radius: 14px;
  background: linear-gradient(135deg, #22c55e, #38bdf8);
  color: #fff;
  font-weight: 900;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dashboard-ranking-body {
  flex: 1;
}

.dashboard-ranking-body .progress {
  height: 8px;
  border-radius: 999px;
  margin-top: 8px;
  background: #dcfce7;
}

/* Status doações */
.dashboard-donation-status {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.dashboard-donation-status > div {
  border-radius: 18px;
  padding: 18px;
  background: linear-gradient(135deg, #f0fdf4, #eff6ff);
  text-align: center;
}

.dashboard-donation-status span {
  display: block;
  color: #64748b;
  font-size: 0.8rem;
  font-weight: 800;
  text-transform: uppercase;
}

.dashboard-donation-status strong {
  display: block;
  margin-top: 6px;
  font-size: 2rem;
  color: #16a34a;
}

/* Forms */
.dashboard-trote-filter .form-control,
.dashboard-trote-filter .btn {
  border-radius: 14px;
}

/* Responsivo */
@media (max-width: 768px) {
  .dashboard-hero {
    padding: 22px;
  }

  .dashboard-actions {
    justify-content: flex-start !important;
  }

  .dashboard-actions .btn {
    margin-left: 0;
    margin-right: 8px;
  }

  .dashboard-section-header {
    align-items: flex-start;
    flex-direction: column;
  }

  .dashboard-donation-status {
    grid-template-columns: 1fr;
  }
}
/* Botão Doar alimentos */
.dashboard-actions .btn-outline-success {
  background: linear-gradient(135deg, #f59e0b, #f97316);
  color: #fff !important;
  border: none;
  box-shadow: 0 8px 18px rgba(249, 115, 22, 0.35);
}

.dashboard-actions .btn-outline-success:hover {
  background: linear-gradient(135deg, #ea580c, #c2410c);
  color: #fff !important;
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(249, 115, 22, 0.45);
}

/* Botão Meu perfil */
.dashboard-actions .btn-outline-secondary {
  background: rgba(255, 255, 255, 0.18);
  color: #fff !important;
  border: 2px solid rgba(255, 255, 255, 0.55);
}

.dashboard-actions .btn-outline-secondary:hover {
  background: #ffffff;
  color: #0284c7 !important;
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(56, 189, 248, 0.35);
}
</style>


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
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="dashboard-eyebrow">Minha jornada</div>
                <h1 class="h3 mb-2 text-gray-900">Minha área</h1>
                <p class="mb-0 text-gray-700">
                    Acompanhe seu trote ativo, registre doacoes e acesse informativos, ranking e certificados em um unico painel.
                </p>
            </div>
            <div class="col-lg-5 mt-3 mt-lg-0">
                <div class="dashboard-actions d-flex flex-wrap justify-content-lg-end">
                    <?= Html::a(
                        '<i class="fas fa-plus-circle mr-2" aria-hidden="true"></i><span>Registrar doacao</span>',
                        ['/participante/doacao/create'],
                        ['class' => 'btn btn-success dashboard-action-primary mb-2']
                    ) ?>

                    <?= Html::a(
                        '<i class="fas fa-apple-alt mr-2" aria-hidden="true"></i><span>Doar alimentos</span>',
                        '#doacao-alimentos',
                        ['class' => 'btn btn-outline-success mb-2']
                    ) ?>

                    <?= Html::a(
                        '<i class="fas fa-user mr-2" aria-hidden="true"></i><span>Meu perfil</span>',
                        ['/participante/users/perfil'],
                        ['class' => 'btn btn-outline-secondary mb-2']
                    ) ?>
                </div>
            </div>
        </div>
    </section>

    <?php if ($banner !== null && (!empty($banner->img_dsk) || !empty($banner->img_mob))): ?>
        <div class="dashboard-banner card shadow-sm border-0 mb-4 overflow-hidden">
            <img
                src="/img/<?= Html::encode(Helper::isMobile() ? ($banner->img_mob ?: $banner->img_dsk) : ($banner->img_dsk ?: $banner->img_mob)) ?>"
                alt="Banner do trote"
            >
        </div>
    <?php endif; ?>

    <?php if ($showStartParticipationCard && $troteAtivoGlobal !== null): ?>
        <section class="card start-participation-card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="dashboard-eyebrow dashboard-eyebrow-light">Trote ativo disponivel</div>
                        <h2 class="h4 mb-2 text-white">Comece a participar do Trote Solidario <?= Html::encode($troteAtivoGlobalDisplayEdition) ?></h2>
                        <p class="mb-0">
                            Informe sua universidade e curso para iniciar sua participacao. O sistema completa usuario, trote e status automaticamente.
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
                <div class="col-lg-7">
                    <div class="dashboard-eyebrow text-success">Trote ativo</div>
                    <?php if ($troteAtivo !== null): ?>
                        <div class="d-flex align-items-center flex-wrap mb-2">
                            <h2 class="h4 mb-0 mr-2 text-gray-900"><?= Html::encode($troteAtivo->titulo ?: ('Trote ' . $troteAtivo->edicao)) ?></h2>
                            <span class="badge badge-success"><?= Html::encode($troteAtivo->getStatusLabel()) ?></span>
                        </div>
                        <p class="mb-3 text-muted">Edicao <?= Html::encode($troteAtivo->edicao) ?></p>
                        <div class="row dashboard-context-metrics">
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                <span>Inicio</span>
                                <strong><?= $troteAtivo->data_inicio ? Yii::$app->formatter->asDate($troteAtivo->data_inicio, 'php:d/m/Y') : '-' ?></strong>
                            </div>
                            <div class="col-sm-4 mb-3 mb-sm-0">
                                <span>Fim</span>
                                <strong><?= $troteAtivo->data_fim ? Yii::$app->formatter->asDate($troteAtivo->data_fim, 'php:d/m/Y') : '-' ?></strong>
                            </div>
                            <div class="col-sm-4">
                                <span>Ranking</span>
                                <strong><?= !empty($ranking) ? 'Atualizado' : 'Sem dados' ?></strong>
                            </div>
                        </div>
                    <?php elseif ($showStartParticipationCard && $troteAtivoGlobal !== null): ?>
                        <h2 class="h4 mb-2 text-gray-900"><?= Html::encode($troteAtivoGlobal->titulo ?: ('Trote ' . $troteAtivoGlobalDisplayEdition)) ?></h2>
                        <p class="mb-0 text-muted">Voce ainda nao iniciou sua participacao neste trote ativo.</p>
                    <?php else: ?>
                        <h2 class="h4 mb-2 text-gray-900">Nenhum trote selecionado</h2>
                        <p class="mb-0 text-muted">Quando houver participacao ativa, os dados do trote aparecerao aqui.</p>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5 mt-4 mt-lg-0">
                    <?php if (!empty($trotesDisponiveis)): ?>
                        <form method="get" action="<?= Html::encode(Url::to(['/participante/default/home'])) ?>" class="dashboard-trote-filter">
                            <label class="small text-muted text-uppercase font-weight-bold" for="dashboard-trote-id">Trote exibido na home</label>
                            <div class="input-group">
                                <select id="dashboard-trote-id" name="trote_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Selecione um trote ativo</option>
                                    <?php foreach ($trotesDisponiveis as $troteOption): ?>
                                        <option value="<?= (int) $troteOption->id ?>" <?= $selectedTroteId === (int) $troteOption->id ? 'selected' : '' ?>>
                                            <?= Html::encode(($troteOption->titulo ?: 'Trote') . ' | ' . ($troteOption->edicao ?? '-')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">Aplicar</button>
                                </div>
                            </div>
                        </form>
                        <?php if ($selectedTroteId === null && $hasMultipleTrotes): ?>
                            <p class="small text-muted mt-2 mb-0">Escolha o trote para atualizar ranking, doacoes e certificados.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="dashboard-empty-note">
                            Sem participacao ativa vinculada a um trote em andamento.
                        </div>
                    <?php endif; ?>
                </div>
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
                        <h2 class="h5 mb-4 text-gray-900">Informativos</h2>
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
                                    <a href="/pdf/<?= Html::encode($informativo->arquivo) ?>" target="_blank"
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
                        <h2 class="h5 mb-4 text-gray-900">Regulamentos</h2>
                    </div>
                    <?php if (!empty($regulamentos)): ?>
                        <ul class="list-group list-group-flush dashboard-list">
                            <?php foreach ($regulamentos as $regulamento): ?>
                                <li class="list-group-item dashboard-list-item d-flex justify-content-between align-items-center">
                                    <span class="dashboard-document-name">
                                        <?= Html::encode($regulamento->nome ?? ('Regulamento #' . $regulamento->id)) ?>
                                    </span>
                                    <a href="/pdf/<?= Html::encode($regulamento->arquivo) ?>" target="_blank"
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

    <section id="doacao-alimentos" class="card dashboard-food-card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="dashboard-eyebrow text-success">Doação de alimentos</div>
                    <h2 class="h4 mb-2 text-gray-900">Escolha uma universidade e doe alimentos online.</h2>
                    <p class="mb-0 text-muted">
                        Cada link direciona para a campanha da universidade escolhida. Selecione os itens, conclua a doacao e ajude instituicoes da cidade sede.
                    </p>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0 text-lg-right">
                    <span class="dashboard-food-count"><?= count($universidadesDoacao) ?> universidade(s) disponivel(is)</span>
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
                    Links de doacao de alimentos ainda nao estao disponiveis.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="row">
        <div class="col-xl-8">
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="dashboard-section-header">
                        <div>
                            <div class="dashboard-eyebrow text-success">Acompanhamento</div>
                            <h2 class="h5 mb-0 text-gray-900">Minhas participações</h2>
                        </div>
                        <?= Html::a('Ver doacoes', ['/participante/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                    </div>

                    <?php if (!empty($participacoes)): ?>
                        <ul class="list-group list-group-flush dashboard-list">
                            <?php foreach ($participacoes as $participacao): ?>
                                <?php
                                $trote = $participacao->trote;
                                $troteLabel = $trote !== null
                                    ? (($trote->titulo ?: 'Trote') . ' | ' . ($trote->edicao ?? '-'))
                                    : 'Trote nao encontrado';
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
                            <span>Voce ainda nao possui participacoes cadastradas.</span>
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
                        <div class="dashboard-empty-state">O ranking ainda nao possui dados suficientes.</div>
                    <?php endif; ?>
                </div>
            </section>
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="dashboard-section-header">
                        <h2 class="h5 mb-0 text-gray-900">Status das doações</h2>
                    </div>
                    <div class="dashboard-donation-status">
                        <div>
                            <span>Aprovadas</span>
                            <strong><?= $totalDoacoesAprovadas ?></strong>
                        </div>
                        <div>
                            <span>Pendentes</span>
                            <strong><?= $totalDoacoesPendentes ?></strong>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
if ($showStartParticipationCard && $troteAtivoGlobal !== null && $shouldOpenStartParticipationModal) {
    $this->registerJs("jQuery('#startParticipationModal').modal('show');");
}
?>
