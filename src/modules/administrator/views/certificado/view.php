<?php

use yii\helpers\Html;

$this->title = 'Certificado';
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><?= Html::encode($this->title) ?></span>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
    <div class="card-body">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Trote Solidario</h2>
            <p class="lead">Certificado de participacao</p>
        </div>

        <p>Certificamos que <strong><?= Html::encode($model->participacao->user->nome ?? '-') ?></strong> participou do Trote Solidario na edicao <strong><?= Html::encode($model->participacao->trote->edicao ?? '-') ?></strong>, vinculado a <strong><?= Html::encode($model->participacao->universidade->nome ?? '-') ?></strong>.</p>

        <p><strong>Evento(s):</strong> <?= Html::encode($model->eventoNomes) ?></p>

        <p>A carga horaria total registrada neste certificado e de <strong><?= (int) $model->carga_horaria_total ?> horas</strong>.</p>

        <div class="row mt-4">
            <div class="col-md-6"><strong>Codigo validador:</strong> <?= Html::encode($model->codigo_validador) ?></div>
            <div class="col-md-6"><strong>Data de emissao:</strong> <?= Yii::$app->formatter->asDatetime($model->data_emissao, 'php:d/m/Y H:i') ?></div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6"><strong>Hash de integridade:</strong> <?= Html::encode($model->hash_integridade) ?></div>
            <div class="col-md-6"><strong>Emitido por:</strong> <?= Html::encode($model->emissor->nome ?? '-') ?></div>
        </div>
    </div>
</div>