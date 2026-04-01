<?php

use yii\helpers\Html;

$this->title = 'Ranking completo';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Ranking completo</h1>
            <p class="mb-0 text-muted">Acompanhe a pontua??o consolidada por universidade no Trote Solid?rio.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <?= Html::a('Voltar para home', ['/participante/default/home'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <?= Html::beginForm(['/participante/default/ranking'], 'get', ['class' => 'row g-3 align-items-end']) ?>
                <div class="col-md-5">
                    <label class="form-label" for="ranking-trote">Edi??o do trote</label>
                    <?= Html::dropDownList('trote_id', $selectedTroteId, $trotes, [
                        'id' => 'ranking-trote',
                        'class' => 'form-control',
                        'prompt' => 'Todos os trotes',
                    ]) ?>
                </div>
                <div class="col-md-auto">
                    <?= Html::submitButton('Filtrar ranking', ['class' => 'btn btn-success']) ?>
                </div>
                <?php if ($selectedTroteId !== null): ?>
                    <div class="col-md-auto">
                        <?= Html::a('Limpar filtro', ['/participante/default/ranking'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                <?php endif; ?>
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if ($troteAtivo !== null): ?>
        <div class="card shadow-sm border-0 mb-4 border-left-success">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="text-success small text-uppercase font-weight-bold">Trote ativo</div>
                        <div class="h5 mb-0"><?= Html::encode($troteAtivo->titulo ?: ('Trote ' . $troteAtivo->edicao)) ?></div>
                    </div>
                    <div class="text-muted small">
                        <?= $selectedTroteId !== null && (int) $selectedTroteId === (int) $troteAtivo->id ? 'Exibindo a edi??o ativa.' : 'Voc? pode filtrar por qualquer edi??o dispon?vel.' ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h2 class="h5 mb-0 text-gray-800">Ranking por universidade</h2>
                <span class="text-muted small"><?= count($ranking) ?> universidade(s)</span>
            </div>

            <?php if (!empty($ranking)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Universidade</th>
                                <th style="width: 140px;">Participantes</th>
                                <th style="width: 120px;">Pontos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ranking as $index => $item): ?>
                                <?php $isCurrentUniversity = in_array((int) ($item['universidade_id'] ?? 0), $userUniversityIds, true); ?>
                                <tr class="<?= $isCurrentUniversity ? 'table-success' : '' ?>">
                                    <td><?= $index + 1 ?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= Html::encode($item['nome']) ?></div>
                                        <?php if ($isCurrentUniversity): ?>
                                            <div class="small text-success">Sua universidade</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= (int) $item['participantes'] ?></td>
                                    <td><strong><?= (int) $item['pontos'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-muted">Ainda n?o h? dados suficientes para montar o ranking completo neste filtro.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
