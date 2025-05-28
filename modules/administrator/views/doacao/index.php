<?php

use app\modules\participante\models\Helper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\participante\models\Users;
use app\modules\participante\models\Universidade;
use app\modules\participante\models\Trote;
use yii\helpers\Url;


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
                        'data-src' =>  Yii::$app->request->hostInfo . $webPath,
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
            return Html::textarea('', $model->validado_motivo, ['onblur' => 'salvaMotivo("' . $model->id . '",this.value)', 'rows' => '10', 'cols' => '30', 'class' => 'form-control']);
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
                return "Ainda não validado";
            }
            return $model->validado == 1 ? "Aprovado" : "Não aprovado";
        }
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

                return Html::a($label, [
                    'validar', // nome da action no controller
                    'id' => $model->id,
                    'status' => $status
                ], [
                    'class' => $btnClass,
                    'data-method' => 'post',
                    'data-confirm' => "Tem certeza que deseja $label essa doação?"
                ]);
            }
        ]
    ],
];

?>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"
/>
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

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Doações</h1>
    </div>
    <!-- Color System -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?= Html::a('Criar Doação', ['create'], ['class' => 'btn btn-success']) ?>
                    </h6>
                    <br>
                    <h6 class="m-0 font-weight-bold text-primary">
                        <?= Html::a('Importador', ['import'], ['class' => 'btn btn-primary']) ?>
                    </h6>

                </div>
                <div class="p-3" style="overflow-x: auto; width: 100%;">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'headerContainer' => ['style' => 'top:50px', 'class' => 'kv-table-header'], // offset from top
                        //'floatHeader' => true, // table header floats when you scroll
                        //'floatPageSummary' => true, // table page summary floats when you scroll
                        //'floatFooter' => false, // disable floating of table footer
                        'pjax' => true, // pjax is set to always false for this demo
                        // parameters from the demo form
                        //  'responsive' => true,
                        //'bordered' => true,
                        //'striped' => true,
                        //'condensed' => true,
                        'hover' => true,
                        //'showPageSummary' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-book"></i>  Usuários',
                            'type' => 'success',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
                        // set export properties
                        'export' => [
                            'fontAwesome' => true
                        ],
                        'exportConfig' => [
                            'html' => [],
                            'csv' => [],
                            'txt' => [],
                            'xls' => [],
                            //'pdf' => [],
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
    function validaDoacao(id, valor) {

        $.ajax({
            url: '/administrator/doacao/check',
            data: {
                "model_id": id,
                "novo_valor": valor
            },
            type: "POST",
            dataType: 'json',
            success: function (result) {
                $('#doacao-div-' + id).html(result.html);
            },
            error: function () {
                alert('Erro: avisar a ti');
            }
        });
    }

    function salvaMotivo(id, texto) {

        $.ajax({
            url: '/administrator/doacao/atualizamotivo',
            data: {
                "model_id": id,
                "texto": texto
            },
            type: "POST",
            dataType: 'json',
            success: function (result) {
                console.log(result);
            },
            error: function () {
                alert('Erro: avisar a ti');
            }
        });
    }

    Fancybox.bind('[data-fancybox]');
</script>