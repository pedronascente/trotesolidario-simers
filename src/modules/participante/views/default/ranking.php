<?php

use app\assets\ParticipantDashboardAsset;
use yii\helpers\Html;

ParticipantDashboardAsset::register($this);
$this->title = 'Ranking completo';
?>

<main class="participant-dashboard ranking-page container-fluid">
    <header class="ranking-page-header">
        <div>
            <div class="dashboard-eyebrow">Desempenho das universidades</div>
            <h1>Ranking completo</h1>
            <p>Acompanhe a pontuação consolidada por universidade no Trote Solidário.</p>
        </div>
        <?= Html::a(
            '<i class="fas fa-arrow-left" aria-hidden="true"></i><span>Voltar para home</span>',
            ['/participante/default/home'],
            ['class' => 'btn ranking-back-button']
        ) ?>
    </header>

    <section class="card ranking-filter-card mb-4" aria-labelledby="ranking-filter-title">
        <div class="card-body">
            <div class="ranking-section-heading">
                <span class="ranking-section-icon" aria-hidden="true"><i class="fas fa-filter"></i></span>
                <div>
                    <h2 id="ranking-filter-title">Filtrar ranking</h2>
                    <p>Escolha uma edição para consultar os resultados.</p>
                </div>
            </div>
            <?= Html::beginForm(['/participante/default/ranking'], 'get', ['class' => 'ranking-filter-form']) ?>
            <div class="ranking-filter-field">
                <label for="ranking-trote">Edição do trote</label>
                <?= Html::dropDownList('trote_id', $selectedTroteId, $trotes, [
                    'id' => 'ranking-trote',
                    'class' => 'form-control',
                    'prompt' => 'Todos os trotes',
                ]) ?>
            </div>
            <div class="ranking-filter-actions">
                <?= Html::submitButton('<i class="fas fa-search" aria-hidden="true"></i><span>Aplicar filtro</span>', ['class' => 'btn btn-success']) ?>
                <?php if ($selectedTroteId !== null): ?>
                    <?= Html::a('Limpar', ['/participante/default/ranking'], ['class' => 'btn btn-outline-secondary']) ?>
                <?php endif; ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </section>

    <?php if ($troteAtivo !== null): ?>
        <aside class="ranking-active-edition mb-4" aria-label="Edição ativa">
            <span class="ranking-active-icon" aria-hidden="true"><i class="fas fa-bolt"></i></span>
            <div class="ranking-active-content">
                <div>
                    <span>Trote ativo</span>
                    <strong><?= Html::encode($troteAtivo->titulo ?: ('Trote ' . $troteAtivo->edicao)) ?></strong>
                </div>
                <p><?= $selectedTroteId !== null && (int) $selectedTroteId === (int) $troteAtivo->id ? 'Você está vendo a edição ativa.' : 'Você pode consultar qualquer edição disponível.' ?></p>
            </div>
        </aside>
    <?php endif; ?>

    <section class="card ranking-results-card" aria-labelledby="ranking-results-title">
        <div class="card-body">
            <div class="ranking-results-header">
                <div>
                    <div class="dashboard-eyebrow">Classificação geral</div>
                    <h2 id="ranking-results-title">Ranking por universidade</h2>
                </div>
                <span class="ranking-count"><?= count($ranking) ?> <?= count($ranking) === 1 ? 'universidade' : 'universidades' ?></span>
            </div>
            <?php if (!empty($ranking)): ?>
                <div class="table-responsive ranking-table-wrapper">
                    <table class="table ranking-table mb-0">
                    <caption class="sr-only">Classificação das universidades por pontos</caption>
                    <thead>
                        <tr>
                            <th scope="col">Posição</th>
                            <th scope="col">Universidade</th>
                            <th scope="col">Participantes</th>
                            <th scope="col">Pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ranking as $index => $item): ?>
                            <?php $isCurrentUniversity = in_array((int) ($item['universidade_id'] ?? 0), $userUniversityIds, true); ?>
                            <tr class="<?= $isCurrentUniversity ? 'is-current-university' : '' ?>">
                                <td data-label="Posição"><span class="ranking-position"><?= $index + 1 ?></span></td>
                                <td data-label="Universidade" class="ranking-university">
                                    <strong><?= Html::encode($item['nome']) ?></strong>
                                    <?php if ($isCurrentUniversity): ?>
                                        <span class="ranking-user-badge"><i class="fas fa-user-check" aria-hidden="true"></i> Sua universidade</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Participantes"><?= (int) $item['participantes'] ?></td>
                                <td data-label="Pontos"><strong class="ranking-points"><?= (int) $item['pontos'] ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="dashboard-empty-state">
                    <i class="fas fa-chart-bar" aria-hidden="true"></i>
                    <span>Ainda não há dados suficientes para montar o ranking nesta edição.</span>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
