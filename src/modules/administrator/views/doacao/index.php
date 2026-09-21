<?php

use app\modules\common\models\Doacao;
use app\modules\common\services\DoacaoArquivoStorage;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

$this->title = 'Doações';
$this->params['breadcrumbs'][] = $this->title;
$reprovarUrl = Json::htmlEncode(Url::to(['reprovar']));
?>

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

    .doacao-gallery-dialog {
        max-width: min(1180px, calc(100vw - 32px));
    }

    .doacao-gallery-content {
        overflow: hidden;
        border: 0;
        border-radius: 14px;
        background: #111827;
        box-shadow: 0 24px 70px rgba(15, 23, 42, 0.4);
    }

    .doacao-gallery-header {
        min-height: 64px;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        background: #1f2937;
    }

    .doacao-gallery-kicker {
        display: block;
        margin-bottom: 2px;
        color: #9ca3af;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .doacao-gallery-stage {
        position: relative;
        display: flex;
        min-height: 440px;
        align-items: center;
        justify-content: center;
        padding: 24px 76px;
        background-color: #0b1120;
        background-image: radial-gradient(circle at center, #253047 0, #111827 60%, #090e19 100%);
    }

    .doacao-gallery-image {
        display: block;
        max-width: 100%;
        max-height: 68vh;
        border-radius: 6px;
        background: #fff;
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.38);
        object-fit: contain;
    }

    .doacao-gallery-nav {
        position: absolute;
        top: 50%;
        width: 46px;
        height: 46px;
        padding: 0;
        transform: translateY(-50%);
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 50%;
        background: rgba(17, 24, 39, 0.78);
        color: #fff;
        transition: background-color 0.2s ease, transform 0.2s ease;
        z-index: 2;
    }

    .doacao-gallery-nav:hover:not(:disabled),
    .doacao-gallery-nav:focus:not(:disabled) {
        transform: translateY(-50%) scale(1.06);
        background: #2563eb;
        color: #fff;
        outline: none;
    }

    .doacao-gallery-nav:disabled {
        opacity: 0.25;
    }

    .doacao-gallery-nav--previous { left: 18px; }
    .doacao-gallery-nav--next { right: 18px; }

    .doacao-gallery-details {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 16px 20px;
        background: #1f2937;
    }

    .doacao-gallery-caption {
        margin: 0;
        color: #f9fafb;
        font-size: 15px;
        font-weight: 600;
    }

    .doacao-gallery-counter {
        margin-top: 3px;
        color: #9ca3af;
        font-size: 13px;
    }

    .doacao-gallery-thumbnails {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 12px 20px 16px;
        background: #1f2937;
        scrollbar-color: #64748b transparent;
    }

    .doacao-gallery-thumbnail {
        flex: 0 0 64px;
        width: 64px;
        height: 54px;
        overflow: hidden;
        padding: 2px;
        border: 2px solid transparent;
        border-radius: 8px;
        background: #374151;
        opacity: 0.62;
        transition: opacity 0.2s ease, border-color 0.2s ease;
    }

    .doacao-gallery-thumbnail:hover,
    .doacao-gallery-thumbnail.is-active {
        border-color: #60a5fa;
        opacity: 1;
    }

    .doacao-gallery-thumbnail img {
        width: 100%;
        height: 100%;
        border-radius: 4px;
        object-fit: cover;
    }

    @media (max-width: 767.98px) {
        .doacao-gallery-dialog { margin: 8px auto; }
        .doacao-gallery-stage { min-height: 360px; padding: 18px 52px; }
        .doacao-gallery-image { max-height: 60vh; }
        .doacao-gallery-nav { width: 40px; height: 40px; }
        .doacao-gallery-nav--previous { left: 6px; }
        .doacao-gallery-nav--next { right: 6px; }
        .doacao-gallery-details { align-items: flex-start; flex-direction: column; }
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
            'id' => 'doacao-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => ['id' => 'doacao-grid-pjax'],
            ],
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
                                    'data-gallery-url' => $fileUrl,
                                    'data-gallery-caption' => 'Doacao #' . $model->id . ' - ' . ($model->tipoDoacao->nome ?? $model->arquivo),
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
                                'class' => 'btn btn-sm btn-outline-primary',
                                'title' => 'Editar',
                                'aria-label' => 'Editar doação',
                                'data-pjax' => '0',
                            ]);
                        },
                        'aprovar' => function ($url, $model) {
                            if ($model->status === Doacao::STATUS_APROVADA) {
                                return '';
                            }

                            return Html::a('<i class="fa fa-check"></i>', ['aprovar', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-outline-success',
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
                                'class' => 'btn btn-sm btn-outline-danger btn-rejeitar',
                                'type' => 'button',
                                'title' => 'Rejeitar',
                                'aria-label' => 'Rejeitar doação',
                                'data-id' => $model->id,
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>

<div class="modal fade" id="modalGaleriaDoacao" tabindex="-1" role="dialog" aria-labelledby="tituloGaleriaDoacao" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered doacao-gallery-dialog" role="document">
        <div class="modal-content text-white doacao-gallery-content">
            <div class="modal-header doacao-gallery-header">
                <div>
                    <span class="doacao-gallery-kicker">Galeria de comprovantes</span>
                    <h5 class="modal-title" id="tituloGaleriaDoacao">Visualizar doação</h5>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="doacao-gallery-stage">
                <button type="button" class="doacao-gallery-nav doacao-gallery-nav--previous" id="galeriaAnterior" aria-label="Comprovante anterior">
                    <i class="fas fa-chevron-left" aria-hidden="true"></i>
                </button>
                <img id="imagemGaleriaDoacao" class="doacao-gallery-image" src="" alt="Comprovante da doação">
                <button type="button" class="doacao-gallery-nav doacao-gallery-nav--next" id="galeriaProxima" aria-label="Próximo comprovante">
                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                </button>
            </div>
            <div class="doacao-gallery-details">
                <div>
                    <p id="legendaGaleriaDoacao" class="doacao-gallery-caption"></p>
                    <div id="contadorGaleriaDoacao" class="doacao-gallery-counter" aria-live="polite"></div>
                </div>
                <a class="btn btn-light btn-sm" id="abrirOriginalDoacao" href="#" target="_blank" rel="noopener">
                    <i class="fas fa-external-link-alt mr-1" aria-hidden="true"></i> Abrir original
                </a>
            </div>
            <div id="miniaturasGaleriaDoacao" class="doacao-gallery-thumbnails" aria-label="Miniaturas dos comprovantes"></div>
        </div>
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
                    <button type="submit" class="btn btn-danger">Confirmar rejeicao</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
var galeriaDoacoes = [];
var indiceGaleriaDoacao = 0;

function atualizarGaleriaDoacao(indice) {
    if (!galeriaDoacoes.length) {
        return;
    }

    indiceGaleriaDoacao = (indice + galeriaDoacoes.length) % galeriaDoacoes.length;
    var item = galeriaDoacoes[indiceGaleriaDoacao];

    $('#imagemGaleriaDoacao').attr('src', item.url);
    $('#legendaGaleriaDoacao').text(item.caption);
    $('#contadorGaleriaDoacao').text((indiceGaleriaDoacao + 1) + ' de ' + galeriaDoacoes.length + ' comprovantes');
    $('#abrirOriginalDoacao').attr('href', item.url);
    $('#galeriaAnterior, #galeriaProxima').prop('disabled', galeriaDoacoes.length < 2);
    var miniaturaAtual = $('#miniaturasGaleriaDoacao .doacao-gallery-thumbnail')
        .removeClass('is-active')
        .attr('aria-current', 'false')
        .eq(indiceGaleriaDoacao)
        .addClass('is-active')
        .attr('aria-current', 'true')
        .get(0);

    if (miniaturaAtual && typeof miniaturaAtual.scrollIntoView === 'function') {
        miniaturaAtual.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
    }
}

function montarMiniaturasGaleriaDoacao() {
    var miniaturas = galeriaDoacoes.map(function (item, indice) {
        return $('<button>', {
            type: 'button',
            class: 'doacao-gallery-thumbnail',
            'data-gallery-index': indice,
            'aria-label': 'Abrir comprovante ' + (indice + 1)
        }).append($('<img>', {
            src: item.url,
            alt: '',
            loading: 'lazy'
        }));
    });

    $('#miniaturasGaleriaDoacao').empty().append(miniaturas);
}

$(document)
    .off('click.doacaoGaleria', '.doacao-thumb-link')
    .on('click.doacaoGaleria', '.doacao-thumb-link', function (event) {
        event.preventDefault();

        galeriaDoacoes = $('.doacao-thumb-link').map(function () {
            return {
                url: $(this).data('gallery-url'),
                caption: $(this).data('gallery-caption') || ''
            };
        }).get();

        var urlAtual = $(this).data('gallery-url');
        var indiceAtual = galeriaDoacoes.findIndex(function (item) {
            return item.url === urlAtual;
        });

        montarMiniaturasGaleriaDoacao();
        atualizarGaleriaDoacao(indiceAtual < 0 ? 0 : indiceAtual);
        $('#modalGaleriaDoacao').modal('show');
    })
    .off('click.doacaoRejeicao', '.btn-rejeitar')
    .on('click.doacaoRejeicao', '.btn-rejeitar', function () {
        var id = $(this).data('id');
        if (!id) {
            alert('Não foi possível identificar a doação. Atualize a página e tente novamente.');
            return;
        }

        $('#rejeicao-id').val(id);
        $('#rejeicao-motivo').val('');
        $('#modalRejeicao').modal('show');
    });

$('#galeriaAnterior').off('click.doacaoGaleria').on('click.doacaoGaleria', function () {
    atualizarGaleriaDoacao(indiceGaleriaDoacao - 1);
});

$('#galeriaProxima').off('click.doacaoGaleria').on('click.doacaoGaleria', function () {
    atualizarGaleriaDoacao(indiceGaleriaDoacao + 1);
});

$('#miniaturasGaleriaDoacao')
    .off('click.doacaoGaleria', '.doacao-gallery-thumbnail')
    .on('click.doacaoGaleria', '.doacao-gallery-thumbnail', function () {
        atualizarGaleriaDoacao(Number($(this).data('gallery-index')));
    });

$(document).off('keydown.doacaoGaleria').on('keydown.doacaoGaleria', function (event) {
    if (!$('#modalGaleriaDoacao').hasClass('show')) {
        return;
    }

    if (event.key === 'ArrowLeft') {
        atualizarGaleriaDoacao(indiceGaleriaDoacao - 1);
    } else if (event.key === 'ArrowRight') {
        atualizarGaleriaDoacao(indiceGaleriaDoacao + 1);
    }
});

$('#modalGaleriaDoacao').off('hidden.bs.modal.doacaoGaleria').on('hidden.bs.modal.doacaoGaleria', function () {
    $('#imagemGaleriaDoacao').attr('src', '');
    $('#miniaturasGaleriaDoacao').empty();
});

$('#formRejeicao').off('submit.doacaoRejeicao').on('submit.doacaoRejeicao', function (e) {
        e.preventDefault();

        var id = $('#rejeicao-id').val();
        var motivo = $('#rejeicao-motivo').val().trim();
        if (!id || motivo === '') {
            alert('Informe o motivo da rejeicao');
            return;
        }

        var csrfData = {};
        csrfData[yii.getCsrfParam()] = yii.getCsrfToken();

        var requestData = $.extend(csrfData, {
            id: $('#rejeicao-id').val(),
            motivo_reprovado: motivo
        });

        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);

        $.post($reprovarUrl, requestData)
            .done(function (response) {
                if (!response || response.success !== true) {
                    alert('Não foi possível reprovar a doação. Tente novamente.');
                    return;
                }

                $('#modalRejeicao').modal('hide');
                $.pjax.reload({ container: '#doacao-grid-pjax', push: false, replace: false });
            })
            .fail(function () {
                alert('Erro ao comunicar com o servidor. Tente novamente.');
            })
            .always(function () {
                submitButton.prop('disabled', false);
            });
});
JS);
?>
