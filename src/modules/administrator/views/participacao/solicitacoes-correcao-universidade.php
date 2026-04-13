<?php

use app\modules\common\models\ParticipacaoUniversidadeChangeRequest;
use kartik\alert\Alert;
use yii\helpers\Html;

$this->title = 'Solicitacoes de correcao de universidade';
$this->params['breadcrumbs'][] = ['label' => 'Participacoes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Solicitacoes', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Solicitacoes', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><?= Html::encode($this->title) ?></h1>
            <p class="mb-0 text-muted">Revise pedidos enviados por participantes quando a autocorrecao nao puder mais ser aplicada diretamente.</p>
        </div>
        <?= Html::a('Voltar para participacoes', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h2 class="h5 mb-0 text-gray-800">Pendentes</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>Participante</th>
                        <th>Trote</th>
                        <th>Atual</th>
                        <th>Solicitada</th>
                        <th>Solicitado em</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($pendentes)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Nao ha solicitacoes pendentes.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pendentes as $request): ?>
                            <tr>
                                <td><?= Html::encode($request->participacao->user->nome ?? '-') ?></td>
                                <td><?= Html::encode($request->participacao->trote->edicao ?? '-') ?></td>
                                <td><?= Html::encode($request->oldUniversidade->nome ?? '-') ?></td>
                                <td><?= Html::encode($request->newUniversidade->nome ?? '-') ?></td>
                                <td><?= Yii::$app->formatter->asDatetime($request->created_at, 'php:d/m/Y H:i') ?></td>
                                <td class="text-right"><?= Html::a('Analisar', ['analisar-correcao-universidade', 'id' => $request->id], ['class' => 'btn btn-sm btn-warning']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h2 class="h5 mb-0 text-gray-800">Historico recente</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Participante</th>
                        <th>Solicitada</th>
                        <th>Status</th>
                        <th>Revisado por</th>
                        <th>Revisado em</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($historico)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma solicitacao registrada.</td></tr>
                    <?php else: ?>
                        <?php foreach ($historico as $request): ?>
                            <tr>
                                <td><?= Html::encode($request->participacao->user->nome ?? '-') ?></td>
                                <td><?= Html::encode($request->newUniversidade->nome ?? '-') ?></td>
                                <td><?= Html::encode(ParticipacaoUniversidadeChangeRequest::getStatusList()[$request->status] ?? $request->status) ?></td>
                                <td><?= Html::encode($request->revisor->nome ?? '-') ?></td>
                                <td><?= $request->reviewed_at ? Yii::$app->formatter->asDatetime($request->reviewed_at, 'php:d/m/Y H:i') : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
