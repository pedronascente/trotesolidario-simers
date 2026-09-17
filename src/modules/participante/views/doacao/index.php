<?php

use app\modules\common\models\Doacao;
use app\modules\common\services\DoacaoArquivoStorage;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Minhas doações';

$this->registerCssFile('@web/css/donation-styles.css');
?>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

<style>
    .doacao-thumb-link { display: inline-flex; align-items: center;justify-content: center;text-decoration: none;}
    .doacao-thumb-img {width: 72px;height: 72px; object-fit: cover;border-radius: 8px; border: 1px solid #dfe3e8; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08); transition: transform 0.2s ease, box-shadow 0.2s ease;cursor: zoom-in; background: #f8fafc;}
    .doacao-thumb-link:hover .doacao-thumb-img {transform: scale(1.06); box-shadow: 0 8px 18px rgba(15, 23, 42, 0.16);}
    .doacao-file-link {white-space: nowrap;}
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
    .btn-group-actions form { margin: 0; }
</style>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Doação', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Doação', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de doações</h6>
        <?= Html::a('<i class="fas fa-plus mr-1" aria-hidden="true"></i> Nova doação', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax' => true,
                'hover' => true,
                'responsive' => true,
                'columns' => [
                    'id',
                    [
                        'attribute' => 'arquivo',
                        'label' => 'Arquivo',
                        'format' => 'raw',
                        'filter' => false,
                        'hAlign' => 'center',
                        'vAlign' => 'center',
                        'value' => function ($model) {
                            if (!$model->arquivo) {
                                return Html::tag('span', 'Sem arquivo');
                            }

                            $filePath = DoacaoArquivoStorage::resolve($model->arquivo);
                            if ($filePath === null) {
                                return Html::tag('span', 'Arquivo indisponível', ['class' => 'text-muted']);
                            }

                            $fileUrl = Url::to(['arquivo', 'id' => $model->id], true);

                            if (DoacaoArquivoStorage::isImage($filePath)) {
                                return Html::a(
                                    Html::img($fileUrl, [
                                        'class' => 'doacao-thumb-img',
                                        'alt' => 'Doacao ' . $model->id,
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]),
                                    $fileUrl,
                                    [
                                        'class' => 'doacao-thumb-link',
                                        'data-fancybox' => 'doacoes-gallery',
                                        'data-src' => $fileUrl,
                                        'data-caption' => 'Doacao #' . $model->id . ' - ' . ($model->tipoDoacao->nome ?? $model->arquivo),
                                        'data-pjax' => '0',
                                    ]
                                );
                            }

                            return Html::a('Abrir arquivo', $fileUrl, [
                                'class' => 'doacao-file-link',
                                'target' => '_blank',
                                'data-pjax' => '0',
                            ]);
                        },
                    ],
                    [
                        'attribute' => 'participacao_id',
                        'label' => 'Participacao',
                        'filter' => $filterData['participacoes'] ?? [],
                        'value' => fn($model) => $model->getParticipacaoDisplay(),
                    ],
                    [
                        'attribute' => 'tipo_doacao_id',
                        'label' => 'Tipo de doacao',
                        'filter' => $filterData['tiposDoacao'] ?? [],
                        'value' => fn($model) => $model->tipoDoacao->nome ?? '-',
                    ],
                    [
                        'attribute' => 'evento_id',
                        'label' => 'Evento',
                        'filter' => $filterData['eventos'] ?? [],
                        'value' => fn($model) => $model->evento->nome ?? '-',
                    ],
                    'cpf_snapshot',
                    'edicao_snapshot',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return match ($model->status) {
                                Doacao::STATUS_APROVADA => Html::tag('span', 'Aprovada', ['class' => 'badge badge-success']),
                                Doacao::STATUS_REJEITADA => Html::tag('span', 'Rejeitada', ['class' => 'badge badge-danger']),
                                default => Html::tag('span', 'Pendente', ['class' => 'badge badge-warning']),
                            };
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => Doacao::getStatusList(),
                        'filterInputOptions' => ['placeholder' => 'Status'],
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                    ],
                    [
                        'attribute' => 'motivo_reprovado',
                        'label' => 'Motivo da reprovacao',
                        'value' => fn($model) => $model->motivo_reprovado ?: '-',
                    ],
                    [
                        'class' => '\\kartik\\grid\\ActionColumn',
                        'template' => '<div class="btn-group-actions">{view}{update}{delete}</div>',
                        'contentOptions' => ['class' => 'text-nowrap'],
                        'headerOptions' => ['style' => 'width: 150px'],
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<span class="fas fa-eye"></span>', ['view', 'id' => $model->id], [
                                    'class' => 'btn btn-info btn-sm',
                                    'title' => 'Visualizar',
                                    'aria-label' => 'Visualizar',
                                    'data-pjax' => '0',
                                ]);
                            },
                            'update' => function ($url, $model) {
                                return in_array($model->status, [Doacao::STATUS_PENDENTE, Doacao::STATUS_REJEITADA], true)
                                    ? Html::a('<span class="fas fa-pencil-alt"></span>', ['update', 'id' => $model->id], [
                                        'class' => 'btn btn-primary btn-sm',
                                        'title' => 'Editar',
                                        'aria-label' => 'Editar',
                                        'data-pjax' => '0',
                                    ])
                                    : '';
                            },
                            'delete' => function ($url, $model) {
                                if ($model->status !== Doacao::STATUS_REJEITADA) {
                                    return '';
                                }

                                return Html::beginForm(['delete', 'id' => $model->id], 'post', [
                                        'onsubmit' => "return confirm('Deseja excluir esta doacao?');",
                                        'data-pjax' => '0',
                                    ])
                                    . Html::submitButton('<span class="fas fa-trash"></span>', [
                                        'class' => 'btn btn-danger btn-sm',
                                        'title' => 'Excluir',
                                        'aria-label' => 'Excluir',
                                    ])
                                    . Html::endForm();
                            },
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
function initParticipantDonationGallery() {
    if (typeof Fancybox !== 'undefined') {
        Fancybox.bind('[data-fancybox="doacoes-gallery"]', {
            groupAll: true,
            Thumbs: {
                autoStart: true,
            },
            Toolbar: {
                display: {
                    left: ['infobar'],
                    middle: ['zoomIn', 'zoomOut', 'toggle1to1', 'rotateCCW', 'rotateCW'],
                    right: ['slideshow', 'thumbs', 'close'],
                },
            },
        });
    }
}

initParticipantDonationGallery();
$(document).on('pjax:end', function () {
    initParticipantDonationGallery();
});
JS);
?>
