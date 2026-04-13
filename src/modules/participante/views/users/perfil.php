<?php

use app\modules\common\models\ParticipacaoUniversidadeChangeRequest;
use kartik\alert\Alert;
use yii\helpers\Html;

$this->title = 'Meu perfil';
$selectedParticipationId = $selectedParticipationId ?? null;
$selectedParticipacao = $selectedParticipacao ?? null;
?>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Perfil', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Perfil', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><?= Html::encode($this->title) ?></h1>
            <p class="mb-0 text-muted">Atualize seus dados pessoais, senha de acesso e informacoes academicas.</p>
        </div>
        <?= Html::a('Voltar para home', ['/participante/default/home'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow border-left-success mb-4">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-2">Resumo da conta</div>
                    <div class="mb-3">
                        <div class="small text-muted">Nome</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($user->nome) ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">E-mail</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($user->email) ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">CPF</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($user->getCpfFormatado() ?? '-') ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Faculdade</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($universidadeAtual ?? '-') ?></div>
                    </div>
                    <div class="mb-0">
                        <div class="small text-muted">Perfil academico</div>
                        <div class="font-weight-bold text-dark">
                            <?= (int) $participante->estudante === 1 ? 'Estudante' : 'Nao estudante' ?>
                            <?= (int) $participante->estudante_medicina === 1 ? ' | Medicina' : '' ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow border-left-info">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-3">Participacoes ativas</div>
                    <?php if (empty($availableParticipacoes)): ?>
                        <div class="alert alert-secondary small mb-3">Voce nao possui participacoes ativas no momento.</div>
                    <?php else: ?>
                        <div class="list-group mb-3">
                            <?php foreach ($availableParticipacoes as $participacaoItem): ?>
                                <?php $isSelected = (int) $participacaoItem->id === (int) $selectedParticipationId; ?>
                                <?= Html::a(
                                    Html::encode(($participacaoItem->trote->edicao ?? 'Sem edicao') . ' | ' . ($participacaoItem->universidade->nome ?? 'Sem universidade')),
                                    ['perfil', 'participacao_id' => $participacaoItem->id],
                                    [
                                        'class' => 'list-group-item list-group-item-action' . ($isSelected ? ' active' : ''),
                                        'encode' => false,
                                    ]
                                ) ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="text-xs font-weight-bold text-info text-uppercase mb-2">Universidade da participacao</div>
                    <?php if ($selectedParticipacao === null): ?>
                        <div class="alert alert-secondary small mb-0">Selecione uma participacao ativa para corrigir ou solicitar revisao da universidade.</div>
                    <?php else: ?>
                        <p class="text-muted small mb-2">Voce esta analisando a participacao do trote <strong><?= Html::encode($selectedParticipacao->trote->edicao ?? '-') ?></strong>.</p>
                        <div class="font-weight-bold text-dark mb-3"><?= Html::encode($selectedParticipacao->universidade->nome ?? 'Nao informada') ?></div>

                        <?php if ($pendingRequest !== null): ?>
                            <div class="alert alert-warning small mb-3">
                                <div><strong>Solicitacao pendente:</strong> <?= Html::encode(ParticipacaoUniversidadeChangeRequest::getStatusList()[$pendingRequest->status] ?? $pendingRequest->status) ?></div>
                                <div><strong>Nova universidade:</strong> <?= Html::encode($pendingRequest->newUniversidade->nome ?? '-') ?></div>
                                <div class="mb-0"><strong>Motivo:</strong> <?= Html::encode($pendingRequest->motivo) ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($canSelfCorrect): ?>
                            <div class="alert alert-info small mb-3">
                                A correcao imediata esta disponivel para a participacao selecionada porque ela ainda nao possui doacoes nem certificados vinculados.
                            </div>
                            <?= Html::a('Corrigir universidade', ['corrigir-universidade', 'participacao_id' => $selectedParticipationId], ['class' => 'btn btn-info btn-sm']) ?>
                        <?php elseif ($canRequestCorrection): ?>
                            <div class="alert alert-secondary small mb-3">
                                A troca imediata nao esta disponivel para esta participacao, mas voce pode abrir uma solicitacao para analise administrativa.
                            </div>
                            <?= Html::a('Solicitar correcao', ['solicitar-correcao-universidade', 'participacao_id' => $selectedParticipationId], ['class' => 'btn btn-outline-info btn-sm']) ?>
                        <?php else: ?>
                            <div class="alert alert-secondary small mb-0">
                                <?= Html::encode($requestCorrectionReason ?: $selfCorrectionReason ?: 'A autocorrecao de universidade nao esta disponivel no momento.') ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h2 class="h5 mb-0 text-gray-800">Editar perfil</h2>
                </div>
                <div class="p-3">
                    <?= $this->render('_form', ['model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
