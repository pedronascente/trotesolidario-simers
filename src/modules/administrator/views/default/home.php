<?php

use app\assets\AdministratorDashboardAsset;
use app\modules\common\models\Doacao;
use yii\helpers\Html;

$this->title = 'Visão geral';
AdministratorDashboardAsset::register($this);

$cards = [
    ['label' => 'Usuários', 'value' => $totalUsuarios, 'hint' => 'Contas cadastradas', 'variant' => 'primary', 'icon' => 'fas fa-users'],
    ['label' => 'Participantes', 'value' => $totalParticipantes, 'hint' => 'Pessoas na jornada', 'variant' => 'success', 'icon' => 'fas fa-user-check'],
    ['label' => 'Doações', 'value' => $totalDoacoes, 'hint' => $doacoesPendentes . ' aguardando análise', 'variant' => 'warning', 'icon' => 'fas fa-hand-holding-heart'],
    ['label' => 'Eventos', 'value' => $totalEventos, 'hint' => 'Eventos cadastrados', 'variant' => 'info', 'icon' => 'fas fa-calendar-alt'],
];

$relatorioDoacoes = [
    ['label' => 'Pendentes', 'value' => $doacoesPendentes, 'variant' => 'warning', 'icon' => 'fas fa-clock'],
    ['label' => 'Aprovadas', 'value' => $doacoesAprovadas, 'variant' => 'success', 'icon' => 'fas fa-check'],
    ['label' => 'Rejeitadas', 'value' => $doacoesRejeitadas, 'variant' => 'danger', 'icon' => 'fas fa-times'],
];

$ativoNoPeriodo = $troteResumo !== null
    && !empty($troteResumo['data_inicio'])
    && !empty($troteResumo['data_fim'])
    && date('Y-m-d') >= $troteResumo['data_inicio']
    && date('Y-m-d') <= $troteResumo['data_fim'];
?>

<main class="administrator-dashboard">
    <header class="administrator-page-header">
        <div>
            <div class="administrator-eyebrow">Painel administrativo</div>
            <h1>Visão geral</h1>
            <p>Acompanhe a operação do Trote Solidário e priorize o que precisa de atenção.</p>
        </div>
        <time datetime="<?= date('c') ?>">Atualizado em <?= Yii::$app->formatter->asDatetime('now', 'php:d/m/Y \à\s H:i') ?></time>
    </header>

    <?php if ($troteResumo !== null): ?>
        <section class="administrator-campaign-card mb-4" aria-labelledby="active-campaign-title">
            <div class="administrator-campaign-main">
                <div class="administrator-eyebrow">Trote em destaque</div>
                <div class="administrator-title-line">
                    <h2 id="active-campaign-title"><?= Html::encode($troteResumo['nome']) ?></h2>
                    <span class="administrator-status-badge"><?= Html::encode($troteResumo['status']) ?></span>
                </div>
                <p>Edição <?= Html::encode($troteResumo['edicao']) ?></p>
                <div class="administrator-campaign-metrics">
                    <div><span>Início</span><strong><?= $troteResumo['data_inicio'] ? Yii::$app->formatter->asDate($troteResumo['data_inicio'], 'php:d/m/Y') : '-' ?></strong></div>
                    <div><span>Encerramento</span><strong><?= $troteResumo['data_fim'] ? Yii::$app->formatter->asDate($troteResumo['data_fim'], 'php:d/m/Y') : '-' ?></strong></div>
                    <div><span>Primeiro evento</span><strong><?= Html::encode($troteResumo['evento'] ?: 'Não definido') ?></strong></div>
                    <div><span>Participações ativas</span><strong><?= (int) $troteResumo['total_participantes'] ?></strong></div>
                </div>
            </div>
            <aside class="administrator-campaign-state <?= $ativoNoPeriodo ? 'is-valid' : 'is-warning' ?>">
                <i class="fas <?= $ativoNoPeriodo ? 'fa-check-circle' : 'fa-exclamation-triangle' ?>" aria-hidden="true"></i>
                <div><strong><?= $ativoNoPeriodo ? 'Período vigente' : 'Verifique as datas' ?></strong><span><?= $ativoNoPeriodo ? 'A edição está dentro da janela configurada.' : 'O status está ativo fora da janela configurada.' ?></span></div>
            </aside>
            <?php if (count($trotesAtivosResumo) > 1): ?>
                <div class="administrator-inline-alert"><i class="fas fa-info-circle" aria-hidden="true"></i><span>Existem <?= count($trotesAtivosResumo) ?> trotes marcados como ativos. Este painel considera a edição mais recente.</span><?= Html::a('Revisar trotes', ['/administrator/trote'], ['class' => 'administrator-text-link']) ?></div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <section class="administrator-empty-highlight mb-4"><i class="fas fa-calendar-plus" aria-hidden="true"></i><div><strong>Nenhum trote ativo</strong><span>Ative uma edição para exibir período, participação e ranking no painel.</span></div><?= Html::a('Gerenciar trotes', ['/administrator/trote'], ['class' => 'btn btn-success btn-sm']) ?></section>
    <?php endif; ?>

    <section class="row administrator-summary-row" aria-label="Indicadores gerais">
        <?php foreach ($cards as $card): ?>
            <div class="col-sm-6 col-xl-3 mb-4"><article class="administrator-summary-card administrator-summary-<?= Html::encode($card['variant']) ?> h-100"><div><span><?= Html::encode($card['label']) ?></span><strong><?= (int) $card['value'] ?></strong><small><?= Html::encode($card['hint']) ?></small></div><i class="<?= Html::encode($card['icon']) ?>" aria-hidden="true"></i></article></div>
        <?php endforeach; ?>
    </section>

    <div class="row">
        <div class="col-xl-8 mb-4"><section class="administrator-card h-100">
            <header class="administrator-card-header"><div><div class="administrator-eyebrow">Moderação</div><h2>Relatório de doações</h2></div><?= Html::a('Abrir módulo', ['/administrator/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?></header>
            <div class="administrator-donation-status">
                <?php foreach ($relatorioDoacoes as $item): ?>
                    <div class="administrator-status administrator-status-<?= Html::encode($item['variant']) ?>"><i class="<?= Html::encode($item['icon']) ?>" aria-hidden="true"></i><span><?= Html::encode($item['label']) ?></span><strong><?= (int) $item['value'] ?></strong></div>
                <?php endforeach; ?>
            </div>
            <p class="administrator-card-note">Status disponíveis: <?= Html::encode(implode(', ', array_values(Doacao::getStatusList()))) ?>.</p>
        </section></div>

        <div class="col-xl-4 mb-4"><section class="administrator-card h-100">
            <header class="administrator-card-header"><div><div class="administrator-eyebrow">Atalhos</div><h2>Ações rápidas</h2></div></header>
            <nav class="administrator-quick-actions" aria-label="Ações administrativas frequentes">
                <?= Html::a('<i class="fas fa-calendar-plus" aria-hidden="true"></i><span>Novo evento</span>', ['/administrator/evento/create']) ?>
                <?= Html::a('<i class="fas fa-plus-circle" aria-hidden="true"></i><span>Novo trote</span>', ['/administrator/trote/create']) ?>
                <?= Html::a('<i class="fas fa-users-cog" aria-hidden="true"></i><span>Usuários</span>', ['/administrator/user']) ?>
                <?= Html::a('<i class="fas fa-user-check" aria-hidden="true"></i><span>Participações</span>', ['/administrator/participacao']) ?>
            </nav>
        </section></div>
    </div>

    <div class="row">
        <div class="col-xl-6 mb-4"><section class="administrator-card h-100">
            <header class="administrator-card-header"><div><div class="administrator-eyebrow">Desempenho</div><h2>Universidades em destaque</h2></div><?= Html::a('Ver ranking', ['/administrator/ranking'], ['class' => 'btn btn-outline-secondary btn-sm']) ?></header>
            <?php if (!empty($rankingUniversidades)): ?>
                <div class="table-responsive"><table class="table administrator-table mb-0"><thead><tr><th>Posição</th><th>Universidade</th><th>Pontos</th><th>Participantes</th></tr></thead><tbody>
                <?php foreach ($rankingUniversidades as $index => $universidade): ?><tr><td><span class="administrator-rank-position"><?= $index + 1 ?></span></td><td><?= Html::encode($universidade['nome']) ?></td><td><strong><?= (int) $universidade['pontos'] ?></strong></td><td><?= (int) $universidade['participantes'] ?></td></tr><?php endforeach; ?>
                </tbody></table></div>
            <?php else: ?><div class="administrator-empty-state"><i class="fas fa-chart-bar" aria-hidden="true"></i><span>O ranking aparecerá quando houver dados suficientes.</span></div><?php endif; ?>
        </section></div>

        <div class="col-xl-6 mb-4"><section class="administrator-card h-100">
            <header class="administrator-card-header"><div><div class="administrator-eyebrow">Cadastros recentes</div><h2>Últimos usuários</h2></div><?= Html::a('Gerenciar', ['/administrator/user'], ['class' => 'btn btn-outline-secondary btn-sm']) ?></header>
            <?php if (!empty($ultimosUsuarios)): ?><ul class="administrator-user-list">
                <?php foreach ($ultimosUsuarios as $user): ?><li><span class="administrator-list-avatar"><i class="fas fa-user" aria-hidden="true"></i></span><div><strong><?= Html::encode($user->nome ?: $user->email) ?></strong><small><?= Html::encode($user->email) ?></small></div><time><?= $user->created_at ? Yii::$app->formatter->asDate($user->created_at, 'php:d/m/Y') : '-' ?></time></li><?php endforeach; ?>
            </ul><?php else: ?><div class="administrator-empty-state"><i class="fas fa-user-plus" aria-hidden="true"></i><span>Nenhum usuário cadastrado.</span></div><?php endif; ?>
        </section></div>
    </div>

    <section class="administrator-card mb-4">
        <header class="administrator-card-header"><div><div class="administrator-eyebrow">Atividade recente</div><h2>Últimas doações</h2></div><?= Html::a('Abrir doações', ['/administrator/doacao'], ['class' => 'btn btn-outline-secondary btn-sm']) ?></header>
        <?php if (!empty($ultimasDoacoes)): ?>
            <div class="table-responsive"><table class="table administrator-table mb-0"><thead><tr><th>Participante</th><th>Tipo</th><th>Status</th><th>Registrada em</th></tr></thead><tbody>
            <?php foreach ($ultimasDoacoes as $doacao): ?><?php $status = Doacao::getStatusList()[$doacao->status] ?? $doacao->status; ?><tr><td><?= Html::encode($doacao->participacao->user->nome ?? '-') ?></td><td><?= Html::encode($doacao->tipoDoacao->nome ?? '-') ?></td><td><span class="administrator-table-status administrator-table-status-<?= Html::encode($doacao->status) ?>"><?= Html::encode($status) ?></span></td><td><?= $doacao->created_at ? Yii::$app->formatter->asDatetime($doacao->created_at, 'php:d/m/Y H:i') : '-' ?></td></tr><?php endforeach; ?>
            </tbody></table></div>
        <?php else: ?><div class="administrator-empty-state"><i class="fas fa-hand-holding-heart" aria-hidden="true"></i><span>Nenhuma doação cadastrada.</span></div><?php endif; ?>
    </section>
</main>
