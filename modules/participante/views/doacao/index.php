<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\participante\models\Universidade;
use app\modules\participante\models\Trote;
?>
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
                                'value' => function ($model) {
                                    return Html::img("/imagens/doacoes/$model->arquivo", ["style" => "height: 80px;width: auto;"]);
                                }
                            ],
                            [
                                'attribute' => 'instituicao',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => ArrayHelper::map(Universidade::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
                                'filterInputOptions' => ['placeholder' => '- Instituição -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                                'value' => function ($model) {
                                    $return = Universidade::find()->where(['id' => $model->instituicao])->one();


                                    return $return->nome;
                                }
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
                                'value' => function ($model) {
                                    return $model->validado ? "Sim" : "Não";
                                }
                            ],
                            [
                                'attribute' => 'validado_motivo',
                                'label' => 'Validados',
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
                                'filter' => ArrayHelper::map(Trote::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
                                'filterInputOptions' => ['placeholder' => '- Trote -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                                'value' => 'trote.nome',
                            ],
                            [
                                'attribute' => 'ativo',
                                'value' => function ($model) {
                                    return ($model->ativo == "1") ? "Ativo" : "Inativo";
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    '1' => 'Ativo',
                                    '0' => 'Inativo',
                                ],
                                'filterInputOptions' => ['placeholder' => '- Status -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '{update} {delete}',
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>