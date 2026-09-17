<?php

use app\modules\common\models\Doacao;
use app\modules\common\services\DoacaoArquivoStorage;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Doações';
$this->params['breadcrumbs'][] = $this->title;
?>

<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />

<style>
    .btn-group-actions {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
    }

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

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Doação', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Doação', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center ">
        <h6 class="m-0 font-weight-bold text-success">Lista de doações</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Nova doação', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'hover' => true,
            'responsive' => true,
            'columns' => [
                //'id',
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
                    'attribute' => 'participacao_label',
                    'label' => 'Participacao',
                    'value' => fn($model) => $model->getParticipacaoDisplay(),
                ],
                [
                    'attribute' => 'tipo_doacao_nome',
                    'label' => 'Tipo de doacao',
                    'value' => fn($model) => $model->tipoDoacao->nome ?? '-',
                ],
                [
                    'attribute' => 'evento_nome',
                    'label' => 'Evento',
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
                    'class' => '\\kartik\\grid\\ActionColumn',
                    'headerOptions' => ['style' => 'width:180px'],
                    'template' => '<div class="btn-group-actions">{update} {aprovar} {reprovar}</div>',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if ($model->status === Doacao::STATUS_APROVADA) {
                                return '';
                            }

                            return Html::a('<i class="fa fa-pencil-alt"></i>', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-primary',
                                'title' => 'Editar',
                                'data-pjax' => '0',
                            ]);
                        },
                        'aprovar' => function ($url, $model) {
                            if ($model->status === Doacao::STATUS_APROVADA) {
                                return '';
                            }

                            return Html::a('<i class="fa fa-check"></i>', ['aprovar', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-success',
                                'title' => 'Aprovar',
                                'data-method' => 'post',
                                'data-confirm' => 'Confirmar aprovacao?',
                                'data-pjax' => '0',
                            ]);
                        },
                        'reprovar' => function ($url, $model) {
                            if ($model->status === Doacao::STATUS_REJEITADA) {
                                return '';
                            }

                            return Html::button('<i class="fa fa-times"></i>', [
                                'class' => 'btn btn-sm btn-danger btn-rejeitar',
                                'title' => 'Rejeitar',
                                'data-id' => $model->id,
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>

<div class="modal fade" id="modalRejeicao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRejeicao" method="post">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Rejeitar Doacao</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejeicao-id">
                    <div class="form-group">
                        <label>Motivo da rejeicao</label>
                        <textarea name="motivo_reprovado" id="rejeicao-motivo" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger">Confirmar rejeicao</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
function initGallery() {
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

function initModalRejeicao() {
    $('.btn-rejeitar').off('click').on('click', function () {
        $('#rejeicao-id').val($(this).data('id'));
        $('#rejeicao-motivo').val('');
        $('#modalRejeicao').modal('show');
    });
}

function initFormRejeicao() {
    $('#formRejeicao').off('submit').on('submit', function (e) {
        e.preventDefault();

        let motivo = $('#rejeicao-motivo').val();
        if (motivo.trim() === '') {
            alert('Informe o motivo da rejeicao');
            return;
        }

        $.post('/administrator/doacao/reprovar', {
            id: $('#rejeicao-id').val(),
            motivo_reprovado: motivo,
            _csrf: yii.getCsrfToken()
        }, function () {
            $('#modalRejeicao').modal('hide');
            $.pjax.reload({ container: '#w0-pjax' });
        });
    });
}

function initPage() {
    initGallery();
    initModalRejeicao();
    initFormRejeicao();
}

initPage();
$(document).on('pjax:end', function () {
    initPage();
});
JS);
?>
