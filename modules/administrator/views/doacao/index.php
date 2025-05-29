<?php

use app\modules\participante\models\Helper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\participante\models\Users;
use app\modules\participante\models\Universidade;
use app\modules\participante\models\Trote;
use yii\helpers\Url;

$this->registerCssFile('@web/css/donation-styles.css');
$gridColumns = [
    [
        'attribute' => 'arquivo',
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'center',
        'filter' => false,
        'value' => function ($model) {
            $filePath = Yii::getAlias('@webroot') . "/imagens/doacoes/" . $model->arquivo;
            $webPath = Yii::getAlias('@web') . "/imagens/doacoes/" . $model->arquivo;


            if (file_exists($filePath) && @getimagesize($filePath)) {
                return Html::a(
                    Html::img(Yii::$app->request->hostInfo . $webPath, [
                        "class" => "image",
                        "style" => "height: 80px; width: auto; cursor: pointer;",
                    ]),
                    Yii::$app->request->hostInfo . $webPath,
                    [
                        'data-fancybox' => 'gallery',
                        'data-src' => Yii::$app->request->hostInfo . $webPath,
                        'data-caption' => 'Doação - ' . $model->arquivo,
                        'class' => 'fancybox-thumb',
                    ]
                );
            } else {
                return Html::tag('p', 'Sem arquivo', ['class' => 'username']);
            }
        }
    ],
    [
        'attribute' => 'user_create',
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'center',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Users::find()->where(['status' => '1'])->all(), 'id', 'name'),
        'filterInputOptions' => ['placeholder' => '- Usuário -'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],

        'value' => function ($model) {
            $user = Users::find()->where(['id' => $model->user_create])->one();
            return $user->name;
        }
    ],
    [
        'attribute' => 'instituicao',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Universidade::find()->all(), 'id', 'nome'),
        'filterInputOptions' => ['placeholder' => '- Instituição -'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
        'value' => function ($model) {
            $return = Universidade::find()->where(['id' => $model->instituicao])->one();


            return $return->nome;
        }
    ],
    [
        'attribute' => 'validado_motivo',
        'contentOptions' => ['style' => 'width:500px !important; white-space: normal;'],
        'value' => function ($model) {
            return Html::textarea('', $model->validado_motivo, [
                'onblur' => 'salvaMotivo("' . $model->id . '",this.value)', 
                'rows' => '3', 
                'cols' => '30', 
                'class' => 'form-control',
                'placeholder' => 'Digite aqui o motivo/observações...'
            ]);
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'validado',
        'label' => 'Validados',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [
            '1' => 'Sim',
            '0' => 'Não',
            '2' => 'Ainda não validado'
        ],
        'filterInputOptions' => ['placeholder' => '- Validados -'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
        'value' => function ($model) {
            if ($model->validado === null) {
                return '<span class="badge badge-warning">Ainda não validado</span>';
            }
            return $model->validado == 1 ? 
                '<span class="badge badge-success">Aprovado</span>' : 
                '<span class="badge badge-danger">Não aprovado</span>';
        },
        'format' => 'raw'
    ],
    [
        'attribute' => 'tipo_doacao',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [
            'Alimentos' => 'Alimentos',
            'Comissão' => 'Comissão',
            'Participação Presencial' => 'Participação Presencial',
            'Sangue' => 'Sangue',

        ],
        'filterInputOptions' => ['placeholder' => '- Tipo de Doação -'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
    ],
    [
        'attribute' => 'user_create_email',
        'label' => 'Usuário Email',
        'format' => 'raw',
        'hAlign' => 'center',
        'vAlign' => 'center',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Users::find()->where(['status' => '1'])->all(), 'id', 'email'),
        'filterInputOptions' => ['placeholder' => '- Email -'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
        'value' => function ($model) {
            $user = Users::find()->where(['id' => $model->user_create])->one();
            return ($user) ? $user->email : "";
        }
    ],
    [
        'attribute' => 'trote_id',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Trote::find()->all(), 'id', 'nome'),
        'filterInputOptions' => ['placeholder' => '- Trote -'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
        'value' => 'trote.nome',
    ],
    [
        'class' => '\kartik\grid\ActionColumn',
        'template' => '{check}',
        'buttons' => [
            'check' => function ($url, $model) {
                $status = $model->validado == 1 ? 0 : 1;
                $label = $model->validado == 1 ? 'Reprovar' : 'Aprovar';
                $btnClass = $model->validado == 1 ? 'btn btn-danger btn-sm' : 'btn btn-success btn-sm';
                $icon = $model->validado == 1 ? 'fa-times' : 'fa-check';

                return Html::button(
                    '<i class="fas ' . $icon . '"></i> ' . $label,
                    [
                        'class' => $btnClass,
                        'onclick' => 'validaDoacao(' . $model->id . ', ' . $status . ')',
                        'data-id' => $model->id
                    ]
                );
            }
        ]
    ],
];

?>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<style>
    .image {
        transition: transform 0.3s ease;
    }

    .image:hover {
        transform: scale(1.1);
    }

    .fancybox-thumb {
        display: inline-block;
        text-decoration: none;
    }
</style>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between py-3">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Doações</h1>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0 mr-2 font-weight-bold text-primary">
                            <?= Html::a('Criar Doação', ['create'], ['class' => 'btn btn-success']) ?>
                        </h6>
                        <h6 class="m-0 font-weight-bold text-primary">
                            <?= Html::a('Importador', ['import'], ['class' => 'btn btn-primary']) ?>
                        </h6>
                    </div>
                </div>
                <div class="" style="overflow-x: auto; width: 100%;">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'headerContainer' => ['style' => 'top:50px', 'class' => 'kv-table-header'], // offset from top
                        'pjax' => true, // pjax is set to always false for this demo
                        'responsive' => true,
                        'hover' => true,
                        'export' => [
                            'fontAwesome' => true
                        ],
                        'exportConfig' => [
                            'html' => [],
                            'csv' => [],
                            'txt' => [],
                            'xls' => [],
                            'json' => [],
                        ],
                        'columns' => $gridColumns,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function showNotification(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        Toast.fire({
            icon: type,
            title: message
        });
    }

    function validaDoacao(id, valor) {
        const btn = $(`button[data-id="${id}"]`);
        const originalHtml = btn.html();
        const statusCell = btn.closest('tr').find('td:nth-child(5)'); // Coluna do status
        
        btn.html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        btn.prop('disabled', true);

        $.ajax({
            url: '/administrator/doacao/check',
            data: {
                "model_id": id,
                "novo_valor": valor
            },
            type: "POST",
            dataType: 'json',
            success: function (result) {
                const novoStatus = valor === 1;
                const novoLabel = novoStatus ? 'Reprovar' : 'Aprovar';
                const novoIcon = novoStatus ? 'fa-times' : 'fa-check';
                const novaBtnClass = novoStatus ? 'btn btn-danger btn-sm' : 'btn btn-success btn-sm';
                const novoStatusHtml = novoStatus ? 
                    '<span class="badge badge-success">Aprovado</span>' : 
                    '<span class="badge badge-danger">Não aprovado</span>';

                btn.html(`<i class="fas ${novoIcon}"></i> ${novoLabel}`);
                btn.removeClass('btn-success btn-danger').addClass(novaBtnClass);
                btn.attr('onclick', `validaDoacao(${id}, ${novoStatus ? 0 : 1})`);
                btn.prop('disabled', false);
                statusCell.html(novoStatusHtml);
                
                showNotification('Status atualizado com sucesso!');
            },
            error: function () {
                btn.html(originalHtml);
                btn.prop('disabled', false);
                showNotification('Erro ao atualizar status. Por favor, tente novamente.', 'error');
            }
        });
    }

    function salvaMotivo(id, texto) {
        const textarea = $(`textarea[data-id="${id}"]`);
        const originalBorder = textarea.css('border-color');
        textarea.css('border-color', '#fed136');

        $.ajax({
            url: '/administrator/doacao/atualizamotivo',
            data: {
                "model_id": id,
                "texto": texto
            },
            type: "POST",
            dataType: 'json',
            success: function (result) {
                textarea.css('border-color', originalBorder);
                showNotification('Motivo salvo com sucesso!');
            },
            error: function () {
                textarea.css('border-color', originalBorder);
                showNotification('Erro ao salvar motivo. Por favor, tente novamente.', 'error');
            }
        });
    }

    Fancybox.bind('[data-fancybox]');
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>