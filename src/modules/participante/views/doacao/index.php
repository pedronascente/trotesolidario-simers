<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\modules\common\models\Universidade;
use app\modules\common\models\Trote;
?>
<style>
.responsive-grid {
    width: 100%;
    max-width: 1800px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    display: inline-block;
}

.status-approved {
    background-color: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.status-rejected {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.status-pending {
    background-color: rgba(254, 209, 54, 0.1);
    color: #fed136;
}

.motivo-box {
    padding: 10px;
    border-radius: 6px;
    background-color: #f8f9fa;
    border-left: 4px solid #dc3545;
    margin-top: 5px;
}

@media (max-width: 768px) {
    .responsive-grid {
        width: 100%;
        overflow-x: auto;
    }
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
                        'options' => ['class' => 'responsive-grid'],
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
                                'label' => 'Status',
                                'format' => 'raw',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    1 => 'Aprovado',
                                    0 => 'Reprovado',
                                    2 => 'Pendente'
                                ],
                                'filterInputOptions' => ['placeholder' => '- Status -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                                'value' => function ($model) {
                                    if ($model->validado === null) {
                                        return '<span class="status-badge status-pending">Pendente</span>';
                                    }
                                    return $model->validado == 1 ? 
                                        '<span class="status-badge status-approved">Aprovado</span>' : 
                                        '<span class="status-badge status-rejected">Reprovado</span>';
                                }
                            ],
                            [
                                'attribute' => 'validado_motivo',
                                'label' => 'Observações',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if (empty($model->validado_motivo)) {
                                        return '-';
                                    }
                                    $class = $model->validado == 0 ? 'danger' : 'success';
                                    return '<div class="motivo-box" style="border-left-color: ' . 
                                           ($model->validado == 0 ? '#dc3545' : '#28a745') . '">' . 
                                           Html::encode($model->validado_motivo) . 
                                           '</div>';
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