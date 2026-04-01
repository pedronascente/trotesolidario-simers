<?php

use app\modules\common\models\Doacao;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->registerCssFile('@web/css/donation-styles.css');
?>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

<style>
    .doacao-thumb-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .doacao-thumb-img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dfe3e8;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: zoom-in;
        background: #f8fafc;
    }

    .doacao-thumb-link:hover .doacao-thumb-img {
        transform: scale(1.06);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.16);
    }

    .doacao-file-link {
        white-space: nowrap;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= Html::a('Criar Doacao', ['create'], ['class' => 'btn btn-success']) ?></h6>
                </div>
                <div class="p-3" style="overflow-x: auto; width: 100%;">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,
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

                                    $filePath = Yii::getAlias('@webroot') . '/imagens/doacoes/' . $model->arquivo;
                                    $webPath = Yii::getAlias('@web') . '/imagens/doacoes/' . $model->arquivo;
                                    $fullUrl = Yii::$app->request->hostInfo . $webPath;

                                    if (file_exists($filePath) && @getimagesize($filePath)) {
                                        return Html::a(
                                            Html::img($fullUrl, [
                                                'class' => 'doacao-thumb-img',
                                                'alt' => 'Doacao ' . $model->id,
                                            ]),
                                            $fullUrl,
                                            [
                                                'class' => 'doacao-thumb-link',
                                                'data-fancybox' => 'doacoes-gallery',
                                                'data-src' => $fullUrl,
                                                'data-caption' => 'Doacao #' . $model->id . ' - ' . ($model->tipoDoacao->nome ?? $model->arquivo),
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    }

                                    return Html::a('Abrir arquivo', $webPath, [
                                        'class' => 'doacao-file-link',
                                        'target' => '_blank',
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ],
                            [
                                'label' => 'Participacao',
                                'value' => fn($model) => $model->getParticipacaoDisplay(),
                            ],
                            [
                                'label' => 'Tipo de doacao',
                                'value' => fn($model) => $model->tipoDoacao->nome ?? '-',
                            ],
                            [
                                'label' => 'Evento',
                                'value' => fn($model) => $model->evento->nome ?? '-',
                            ],
                            'cpf_snapshot',
                            'edicao_snapshot',
                            [
                                'attribute' => 'status',
                                'filter' => Doacao::getStatusList(),
                            ],
                            [
                                'attribute' => 'motivo_reprovado',
                                'label' => 'Motivo da reprovacao',
                                'value' => fn($model) => $model->motivo_reprovado ?: '-',
                            ],
                            [
                                'class' => '\\kartik\\grid\\ActionColumn',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        return in_array($model->status, [Doacao::STATUS_PENDENTE, Doacao::STATUS_REJEITADA], true)
                                            ? Html::a('<span class="fas fa-pencil-alt"></span>', ['update', 'id' => $model->id])
                                            : '';
                                    },
                                    'delete' => function ($url, $model) {
                                        if (!in_array($model->status, [Doacao::STATUS_PENDENTE, Doacao::STATUS_REJEITADA], true)) {
                                            return '';
                                        }

                                        return Html::beginForm(['delete', 'id' => $model->id], 'post', [
                                                'style' => 'display:inline-block;margin:0;',
                                                'onsubmit' => "return confirm('Deseja excluir esta doacao?');",
                                                'data-pjax' => '0',
                                            ])
                                            . Html::submitButton('<span class="fas fa-trash"></span>', [
                                                'class' => 'btn btn-link btn-sm p-0 align-baseline',
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