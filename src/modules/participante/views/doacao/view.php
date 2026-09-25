<?php

use app\assets\ParticipantDashboardAsset;
use app\modules\common\models\Doacao;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Doacao $model */
/** @var bool $arquivoDisponivel */
/** @var bool $arquivoImagem */

$this->title = 'Detalhes da doação';
$this->params['breadcrumbs'][] = ['label' => 'Doações', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
ParticipantDashboardAsset::register($this);
$this->registerCssFile('@web/css/participant-donation-view.css');

$statusOptions = [
    Doacao::STATUS_PENDENTE => [
        'label' => 'Em análise',
        'class' => 'is-pending',
        'icon' => 'fa-clock',
        'message' => 'Seu comprovante foi recebido e aguarda a análise da equipe.',
    ],
    Doacao::STATUS_APROVADA => [
        'label' => 'Aprovada',
        'class' => 'is-approved',
        'icon' => 'fa-check-circle',
        'message' => 'Tudo certo! Sua doação foi conferida e aprovada.',
    ],
    Doacao::STATUS_REJEITADA => [
        'label' => 'Ajuste necessário',
        'class' => 'is-rejected',
        'icon' => 'fa-exclamation-circle',
        'message' => 'Revise a orientação abaixo e envie o comprovante novamente.',
    ],
];
$status = $statusOptions[$model->status] ?? [
    'label' => Doacao::getStatusList()[$model->status] ?? (string) $model->status,
    'class' => 'is-pending',
    'icon' => 'fa-info-circle',
    'message' => 'Acompanhe por aqui a situação da sua doação.',
];

$fileUrl = $arquivoDisponivel ? Url::to(['arquivo', 'id' => $model->id]) : null;
$fileExtension = $model->arquivo ? strtolower((string) pathinfo($model->arquivo, PATHINFO_EXTENSION)) : '';
$canEdit = in_array($model->status, [Doacao::STATUS_PENDENTE, Doacao::STATUS_REJEITADA], true);
$formatDate = static function ($value): string {
    return $value ? Yii::$app->formatter->asDatetime($value, 'php:d/m/Y \à\s H:i') : '-';
};
?>

<main class="container-fluid participant-dashboard participant-donation-view">
    <div class="donation-view-toolbar">
        <?= Html::a(
            '<i class="fas fa-arrow-left" aria-hidden="true"></i><span>Minhas doações</span>',
            ['index'],
            ['class' => 'donation-back-link']
        ) ?>
    </div>

    <section class="donation-summary-card <?= Html::encode($status['class']) ?>" aria-labelledby="donation-view-title">
        <div class="donation-summary-content">
            <div class="donation-summary-icon" aria-hidden="true">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="donation-summary-copy">
                <span class="donation-eyebrow">Doação #<?= Html::encode($model->id) ?></span>
                <h1 id="donation-view-title"><?= Html::encode($model->tipoDoacao->nome ?? 'Doação') ?></h1>
                <p><?= Html::encode($model->evento->nome ?? 'Evento não informado') ?></p>
            </div>
        </div>

        <div class="donation-status-panel" role="status">
            <span class="donation-status-badge">
                <i class="fas <?= Html::encode($status['icon']) ?>" aria-hidden="true"></i>
                <?= Html::encode($status['label']) ?>
            </span>
            <p><?= Html::encode($status['message']) ?></p>
        </div>
    </section>

    <?php if ($model->isTipoDoacaoSangue()): ?>
        <section class="alert alert-warning shadow-sm d-flex align-items-start" role="note" aria-labelledby="donation-blood-term-title">
            <i class="fas fa-exclamation-triangle fa-lg mr-3 mt-1" aria-hidden="true"></i>
            <div>
                <h2 id="donation-blood-term-title" class="h5 font-weight-bold mb-1">Termo da doação de sangue</h2>
                <p class="mb-0">
                    Estou ciente de que a doação deve ser realizada pelo próprio participante, não sendo permitidas doações de terceiros.
                </p>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($model->status === Doacao::STATUS_REJEITADA && $model->motivo_reprovado): ?>
        <section class="donation-feedback-card" aria-labelledby="donation-feedback-title">
            <div class="donation-section-icon" aria-hidden="true"><i class="fas fa-comment-alt"></i></div>
            <div>
                <h2 id="donation-feedback-title">O que precisa ser ajustado</h2>
                <p><?= nl2br(Html::encode($model->motivo_reprovado)) ?></p>
            </div>
        </section>
    <?php endif; ?>

    <div class="donation-view-grid">
        <section class="donation-info-card" aria-labelledby="donation-info-title">
            <div class="donation-section-heading">
                <div class="donation-section-icon" aria-hidden="true"><i class="fas fa-info"></i></div>
                <div>
                    <span class="donation-eyebrow">Resumo</span>
                    <h2 id="donation-info-title">Informações da doação</h2>
                </div>
            </div>

            <dl class="donation-detail-list">
                <div class="donation-detail-item">
                    <dt>Tipo de doação</dt>
                    <dd><?= Html::encode($model->tipoDoacao->nome ?? '-') ?></dd>
                </div>
                <div class="donation-detail-item">
                    <dt>Evento</dt>
                    <dd><?= Html::encode($model->evento->nome ?? '-') ?></dd>
                </div>
                <div class="donation-detail-item">
                    <dt>Edição</dt>
                    <dd><?= Html::encode($model->edicao_snapshot ?: '-') ?></dd>
                </div>
                <div class="donation-detail-item">
                    <dt>CPF registrado</dt>
                    <dd><?= Html::encode($model->cpf_snapshot ?: '-') ?></dd>
                </div>
            </dl>
        </section>

        <section class="donation-receipt-card" aria-labelledby="donation-receipt-title">
            <div class="donation-section-heading">
                <div class="donation-section-icon" aria-hidden="true"><i class="fas fa-paperclip"></i></div>
                <div>
                    <span class="donation-eyebrow">Comprovante</span>
                    <h2 id="donation-receipt-title">Arquivo enviado</h2>
                </div>
            </div>

            <?php if ($fileUrl): ?>
                <?php if ($arquivoImagem): ?>
                    <?= Html::a(
                        Html::img($fileUrl, [
                            'class' => 'donation-receipt-preview',
                            'alt' => 'Prévia do comprovante da doação',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                        ]),
                        $fileUrl,
                        [
                            'class' => 'donation-receipt-preview-link',
                            'target' => '_blank',
                            'rel' => 'noopener',
                            'aria-label' => 'Abrir comprovante em tamanho completo',
                        ]
                    ) ?>
                <?php else: ?>
                    <div class="donation-file-placeholder" aria-hidden="true">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                <?php endif; ?>

                <?= Html::a(
                    '<i class="fas fa-external-link-alt" aria-hidden="true"></i><span>Abrir comprovante</span>',
                    $fileUrl,
                    [
                        'class' => 'btn btn-outline-success donation-receipt-button',
                        'target' => '_blank',
                        'rel' => 'noopener',
                        'data-pjax' => '0',
                    ]
                ) ?>
                <small><?= Html::encode(strtoupper($fileExtension ?: 'Arquivo')) ?> enviado</small>
            <?php else: ?>
                <div class="donation-empty-file">
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                    <p>Nenhum comprovante foi anexado.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <section class="donation-history-card" aria-labelledby="donation-history-title">
        <div class="donation-section-heading">
            <div class="donation-section-icon" aria-hidden="true"><i class="fas fa-history"></i></div>
            <div>
                <span class="donation-eyebrow">Histórico</span>
                <h2 id="donation-history-title">Datas do registro</h2>
            </div>
        </div>
        <div class="donation-history-list">
            <div>
                <span>Enviado em</span>
                <strong><?= Html::encode($formatDate($model->created_at)) ?></strong>
            </div>
            <div>
                <span>Última atualização</span>
                <strong><?= Html::encode($formatDate($model->updated_at)) ?></strong>
            </div>
        </div>
    </section>

    <div class="donation-action-bar">
        <?= Html::a(
            '<i class="fas fa-arrow-left" aria-hidden="true"></i><span>Voltar</span>',
            ['index'],
            ['class' => 'btn btn-outline-secondary donation-action-button']
        ) ?>
        <?php if ($canEdit): ?>
            <?= Html::a(
                '<i class="fas fa-pen" aria-hidden="true"></i><span>Editar doação</span>',
                ['update', 'id' => $model->id],
                ['class' => 'btn btn-success donation-action-button donation-primary-action']
            ) ?>
        <?php endif; ?>
    </div>
</main>
