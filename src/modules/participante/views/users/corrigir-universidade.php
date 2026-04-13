<?php

use kartik\alert\Alert;
use yii\helpers\Html;

$this->title = 'Corrigir universidade';
$currentUniversity = $participacao && $participacao->universidade ? $participacao->universidade->nome : 'Nao informada';
$selectedParticipationId = $selectedParticipationId ?? null;
?>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Universidade', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Universidade', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><?= Html::encode($this->title) ?></h1>
            <p class="mb-0 text-muted">Atualize a universidade da participacao selecionada quando ela ainda nao tiver gerado impacto operacional.</p>
        </div>
        <?= Html::a('Voltar para perfil', ['perfil', 'participacao_id' => $selectedParticipationId], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow border-left-info h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-2">Participacao selecionada</div>
                    <div class="mb-3">
                        <div class="small text-muted">Trote</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($participacao->trote->edicao ?? '-') ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Universidade atual</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($currentUniversity) ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Status da participacao</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($participacao->status ?? '-') ?></div>
                    </div>
                    <div class="alert alert-warning small mb-0">
                        A troca e imediata e altera somente a universidade vinculada a esta participacao ativa. Este fluxo so fica disponivel antes de existirem doacoes ou certificados associados.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h2 class="h5 mb-0 text-gray-800">Nova universidade</h2>
                </div>
                <div class="card-body">
                    <?= $this->render('_university_correction_form', [
                        'model' => $model,
                        'universidades' => $universidades,
                        'selectedParticipationId' => $selectedParticipationId,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
