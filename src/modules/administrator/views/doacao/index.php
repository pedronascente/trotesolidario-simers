<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;

$this->title = 'Doação';
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .btn-group-actions {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
    }

    .img-doacao {
        transition: transform .25s;
        cursor: pointer;
    }

    .img-doacao:hover {
        transform: scale(1.1);
    }
</style>

<div class="container-fluid">

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_SUCCESS,
            'title' => 'Informativo',
            'icon' => 'fas fa-check-circle',
            'body' => Yii::$app->session->getFlash('success'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_DANGER,
            'title' => 'Informativo',
            'icon' => 'fas fa-times-circle',
            'body' => Yii::$app->session->getFlash('error'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12 mb-4">

            <div class="card shadow mb-4">
                <div class="p-3">

                    <p>
                        <?= Html::a('Criar Doação', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <?= GridView::widget([

                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,

                        'panel' => [
                            'heading' => '<i class="fa fa-hand-holding-heart"></i> Lista de Doações'
                        ],

                        'export' => ['fontAwesome' => true],
                        'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],

                        'columns' => [

                            [
                                'attribute' => 'arquivo',
                                'label' => 'Arquivo',
                                'format' => 'raw',
                                'headerOptions' => ['style' => 'width:7%'],
                                'hAlign' => 'center',
                                'vAlign' => 'center',
                                'filter' => false,

                                'value' => function ($model) {

                                    if (!$model->arquivo) {
                                        return Html::tag('span', 'Sem arquivo');
                                    }

                                    $filePath = Yii::getAlias('@webroot') . "/imagens/doacoes/" . $model->arquivo;
                                    $webPath  = Yii::getAlias('@web') . "/imagens/doacoes/" . $model->arquivo;

                                    if (file_exists($filePath) && @getimagesize($filePath)) {
                                        $url = Yii::$app->request->hostInfo . $webPath;
                                        return Html::img($url, [
                                            'class' => 'img-thumbnail img-doacao abrir-modal',
                                            'style' => 'height:70px;width:70px;object-fit:cover;border-radius:6px',
                                            'data-img' => $url,

                                        ]);
                                    }
                                    return Html::tag('span', 'Sem arquivo');
                                }
                            ],
                            [
                                'attribute' => 'user_id',
                                'label' => 'Usuário',
                                'value' => fn($model) => $model->user->name ?? '-',
                                'filter' => \yii\helpers\ArrayHelper::map(
                                    \app\modules\common\models\Users::find()->all(),
                                    'id',
                                    'name'
                                )
                            ],
                            [
                                'attribute' => 'universidade_nome',
                                'label' => 'Universidade',
                                'value' => fn($model) => $model->universidade->nome ?? '-'
                            ],
                            [
                                'attribute' => 'trote_titulo',
                                'label' => 'Trote',
                                'value' => fn($model) => $model->trote->titulo ?? '-'
                            ],
                            [
                                'attribute' => 'evento_nome',
                                'label' => 'Evento',
                                'value' => fn($model) => $model->evento->nome ?? '-'
                            ],
                            [
                                'attribute' => 'tipo_doacao_id',
                                'label' => 'Tipo Doação',
                                'value' => fn($model) => $model->tipoDoacao->nome ?? '-'
                            ],
                            [
                                'attribute' => 'status',
                                'label' => 'Status',
                                'format' => 'raw',
                                'headerOptions' => ['style' => 'width:10%'],

                                'value' => function ($model) {

                                    switch ($model->status) {

                                        case 'aprovado':
                                            return Html::tag(
                                                'span',
                                                'Aprovado',
                                                ['class' => 'badge badge-success']
                                            );

                                        case 'pendente':
                                            return Html::tag(
                                                'span',
                                                'Pendente',
                                                ['class' => 'badge badge-warning']
                                            );

                                        case 'rejeitado':

                                            $tooltip = $model->observacao
                                                ? 'Motivo: ' . Html::encode($model->observacao)
                                                : 'Sem motivo informado';

                                            return
                                                Html::tag(
                                                    'span',
                                                    'Rejeitado',
                                                    ['class' => 'badge badge-danger']
                                                )
                                                .
                                                ' '
                                                .
                                                Html::tag(
                                                    'i',
                                                    '',
                                                    [
                                                        'class' => 'fa fa-info-circle text-danger',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => $tooltip,
                                                        'style' => 'cursor:pointer'
                                                    ]
                                                );

                                        default:
                                            return '-';
                                    }
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    'aprovado' => 'Aprovado',
                                    'pendente' => 'Pendente',
                                    'rejeitado' => 'Rejeitado'
                                ],
                                'filterInputOptions' => [
                                    'placeholder' => 'Status'
                                ],
                                'filterWidgetOptions' => [
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ]
                                ]
                            ],

                            [
                                'class' => '\kartik\grid\ActionColumn',
                                'headerOptions' => ['style' => 'width:150px'],
                                'template' => '<div class="btn-group-actions">{aprovar} {reprovar}</div>',

                                'buttons' => [
                                    'aprovar' => function ($url, $model) {

                                        if ($model->status === 'aprovado') {
                                            return '';
                                        }

                                        return Html::a(
                                            '<i class="fa fa-check"></i>',
                                            ['aprovar', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm btn-success',
                                                'title' => 'Aprovar',
                                                'data-method' => 'post',
                                                'data-confirm' => 'Confirmar aprovação?',
                                                'data-pjax' => '0'
                                            ]
                                        );
                                    },

                                    'reprovar' => function ($url, $model) {

                                        if ($model->status === 'rejeitado') {
                                            return '';
                                        }

                                        return Html::button(
                                            '<i class="fa fa-times"></i>',
                                            [
                                                'class' => 'btn btn-sm btn-danger btn-rejeitar',
                                                'title' => 'Rejeitar',
                                                'data-id' => $model->id,
                                            ]
                                        );
                                    }
                                ]
                            ]
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImagem" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    Visualizar imagem
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="imagemModal" src="" style="max-width:100%;max-height:80vh;border-radius:6px">
            </div>

            <div class="modal-footer">
                <a id="downloadImagem" class="btn btn-success" download>
                    <i class="fa fa-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRejeicao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRejeicao" method="post">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        Rejeitar Doação
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="rejeicao-id">
                    <div class="form-group">
                        <label>Motivo da rejeição</label>
                        <textarea
                            name="observacao"
                            id="rejeicao-observacao"
                            class="form-control"
                            rows="4"
                            required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger">
                        Confirmar Rejeição
                    </button>
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

$this->registerJs(<<<JS

/* ================================
   MODAL DE IMAGEM
================================ */
function initModalImagem(){
    $('.abrir-modal').off('click').on('click', function(){
        let src = $(this).data('img');
        $('#imagemModal').attr('src', src);
        $('#downloadImagem').attr('href', src);
        $('#modalImagem').modal('show');
    });
}

/* ================================
   MODAL DE REJEIÇÃO
================================ */
function initModalRejeicao(){

    $('.btn-rejeitar').off('click').on('click', function(){
        let id = $(this).data('id');
        $('#rejeicao-id').val(id);
        $('#rejeicao-observacao').val('');
        $('#modalRejeicao').modal('show');
    });

}

/* ================================
   ENVIO DO FORMULÁRIO DE REJEIÇÃO
================================ */
function initFormRejeicao(){

    $('#formRejeicao').off('submit').on('submit', function(e){
        e.preventDefault();
        let id  = $('#rejeicao-id').val();
        let obs = $('#rejeicao-observacao').val();
        if(obs.trim() === ''){
            alert('Informe o motivo da rejeição');
            return;
        }
        $.post('/administrator/doacao/reprovar', {
            id: id,
            observacao: obs,
            _csrf: yii.getCsrfToken()
        }, function(){
            $('#modalRejeicao').modal('hide');
            $.pjax.reload({
                container:'#w0-pjax'
            });
        });
    });

}

/* ================================
   TOOLTIP
================================ */
function initTooltip(){

    $('[data-toggle="tooltip"]').tooltip();

}

/* ================================
   INICIALIZAÇÃO GERAL
================================ */
function initPage(){
    initModalImagem();
    initModalRejeicao();
    initFormRejeicao();
    initTooltip();
}

/* primeira carga */
initPage();

/* após atualização PJAX */
$(document).on('pjax:end', function(){
    initPage();
});

JS);

?>


