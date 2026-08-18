<?php

use app\modules\common\models\ParticipacaoUniversidadeChangeRequest;
use app\modules\common\models\UniversityCorrectionReviewForm;
use kartik\alert\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Analisar correcao de universidade';
$this->params['breadcrumbs'][] = ['label' => 'Participacoes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Solicitacoes de correcao de universidade', 'url' => ['solicitacoes-correcao-universidade']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Analise', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><?= Html::encode($this->title) ?></h1>
            <p class="mb-0 text-muted">Revise o contexto da solicitacao e decida se a universidade da participacao deve ser alterada.</p>
        </div>
        <?= Html::a('Voltar para solicitacoes', ['solicitacoes-correcao-universidade'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100 border-left-warning">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-2">Solicitacao</div>
                    <div class="mb-3"><strong>Participante:</strong> <?= Html::encode($requestModel->participacao->user->nome ?? '-') ?></div>
                    <div class="mb-3"><strong>Trote:</strong> <?= Html::encode($requestModel->participacao->trote->edicao ?? '-') ?></div>
                    <div class="mb-3"><strong>Universidade atual:</strong> <?= Html::encode($requestModel->oldUniversidade->nome ?? '-') ?></div>
                    <div class="mb-3"><strong>Universidade solicitada:</strong> <?= Html::encode($requestModel->newUniversidade->nome ?? '-') ?></div>
                    <div class="mb-3"><strong>Status:</strong> <?= Html::encode(ParticipacaoUniversidadeChangeRequest::getStatusList()[$requestModel->status] ?? $requestModel->status) ?></div>
                    <div class="mb-0"><strong>Motivo informado:</strong><br><?= nl2br(Html::encode($requestModel->motivo)) ?></div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h2 class="h5 mb-0 text-gray-800">Decisao administrativa</h2>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
                    <?= $form->field($model, 'decision')->dropDownList([
                        UniversityCorrectionReviewForm::DECISION_APPROVE => 'Aprovar',
                        UniversityCorrectionReviewForm::DECISION_REJECT => 'Reprovar',
                    ], ['prompt' => 'Selecione a decisao']) ?>
                    <?= $form->field($model, 'review_notes')->textarea(['rows' => 5, 'placeholder' => 'Explique a decisao, especialmente em caso de reprovacao.']) ?>
                    <div class="alert alert-info small">
                        Ao aprovar, a universidade da participacao sera alterada, o ranking do trote sera reconstruido e, se existir certificado emitido, o PDF sera regenerado para manter consistencia.
                    </div>
                    <div class="form-group mt-3 mb-0 d-flex gap-2">
                        <?= Html::submitButton('Salvar decisao', ['class' => 'btn btn-warning']) ?>
                        <?= Html::a('Cancelar', ['solicitacoes-correcao-universidade'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
