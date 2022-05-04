<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\admin\models\Users;
use app\modules\admin\models\Universidade;
use app\modules\admin\models\Trote;
?>
<style>
    .image:hover {
        margin:0;
        padding:0;
        width:400% !important;
        height:400% !important;
        z-index: 999 !important;
        position: relative;
        display: block;
        /*        position: absolute;
                display: flex;
                left: 20%;
                top: 10%;
                width: auto;
                height: 500px !important;*/

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
                    <h6 class="m-0 font-weight-bold text-primary"><?= Html::a('Criar Doação', ['create'], ['class' => 'btn btn-success']) ?>
                    </h6>
                </div>
                <div class="p-3" style="overflow-x: auto; width: 100%;"> 
                    <?=
                    GridView::widget([
                        'options' => ['style' => ['width' => '1800px']],
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'arquivo',
                                'format' => 'raw',
                                'hAlign' => 'center',
                                'vAlign' => 'center',
                                'filter' => false,
                                'value' => function($model) {
                                    return Html::img("/imagens/doacoes/$model->arquivo", ["class" => "image", "style" => "height: 80px;width: auto;"]);
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
                               
                                'value' => function($model) {
                                    $user = Users::find()->where(['id'=>$model->user_create])->one();
                                    return $user->name;
                                }
                            ],
                            [
                                'attribute' => 'instituicao',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => ArrayHelper::map(Universidade::find()->all(), 'id', 'nome'),
                                'filterInputOptions' => ['placeholder' => '- Instituição -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                                'value' => function($model) {
                                    $return = Universidade::find()->where(['id'=>$model->instituicao])->one();
                                    
                                    
                                    return $return->nome;
                                }
                            ],
                            [
                                'attribute' => 'validado_motivo',
                                'contentOptions' => ['style' => 'width:500px !important; white-space: normal;'],
                                'value' => function($model) {
                                    return Html::textarea('', $model->validado_motivo, ['onblur' => 'salvaMotivo("' . $model->id . '",this.value)', 'rows' => '10', 'cols' => '30', 'class' => 'form-control']);
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'validado',
                                'label' => 'Validados',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    1 => 'Sim',
                                    0 => 'Não'
                                ],
                                'filterInputOptions' => ['placeholder' => '- Validados -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                                'value' => function($model) {
                                    $return = '';
                                    if ($model->validado === 1) {
                                        $return = "Aprovado";
                                    } elseif ($model->validado === 0) {
                                        $return = "Não aprovado";
                                    } else {
                                        $return = "Ainda não validado";
                                    }
                                    return $return;
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

                                        $validado = [];
                                        if ($model->validado === 1) {
                                            $validado = ["far fa-check-square ", "Desaprovar"];
                                        } elseif ($model->validado === 0) {
                                            $validado = ["far fa-square ", "Aprovar"];
                                        } else {
                                            $validado = ["far fa-square ", "Aprovar"];
                                        }
                                        return '<div id="doacao-div-'.$model->id.'">'.Html::a(
                                                        '<i class="' . $validado[0] . '" title="' . $validado[1] . '" data-toggle="tooltip"></i>', "#", [
                                                    'title' => '',
                                                            'id'=>'doacao-'.$model->id,
                                                    'data-pjax' => '0',
                                                            'onclick'=>'validaDoacao("' . $model->id . '")',
                                                        ]
                                        ).'</div>';
                                    },
                                ],
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function validaDoacao(id) {

        $.ajax({
            url: '/administrator/doacao/check',
            data: {
                "model_id": id
            },
            type: "POST",
            dataType: 'json',
            success: function (result) {
                $('#doacao-' + id).remove();
                $('#doacao-div-' + id).append(
                        '<a href="#" id="doacao' + id + '" onclick="validaDoacao(' + id + ')"> <i title="' + result[1] + '" class="' + result[0] + '"></i></a>'
                        );


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



</script>