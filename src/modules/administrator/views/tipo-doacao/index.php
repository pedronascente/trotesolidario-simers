<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\TipoDoacaoSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipo Doação';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions {
        display: flex;
        gap: 6px;
        justify-content: center;
        align-items: center;
    }
</style>

<div class="container-fluid">

    <!-- Alertas -->
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

    <!-- Cabeçalho -->
    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
    </div> -->

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3">

                    <p>
                        <?= Html::a('Criar Tipo de Doação', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-hand-holding-heart"></i> Lista de Tipos Doeações',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
                        'export' => ['fontAwesome' => true],
                        'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            'nome',
                            'descricao',
                            [
                                'attribute' => 'carga_horaria',
                                'label' => 'Carga Horária',
                            ],
                            [
                                'attribute' => 'pontuacao_ranking',
                                'label' => 'Pontuação',
                            ],

                            [
                                'attribute' => 'ativo',
                                'format'    => 'raw',
                                'filter'    => [1 => 'Sim', 0 => 'Não'],
                                'value'     => function ($model) {
                                    return $model->ativo
                                        ? '<span class="badge badge-success">Sim</span>'
                                        : '<span class="badge badge-secondary">Não</span>';
                                },
                            ],

                            [
                                'headerOptions' => ['style' => 'width:12%'],
                                'class'         => '\kartik\grid\ActionColumn',
                                'template'      => '<div class="btn-group-actions">{update} {delete}</div>',
                                'buttons'       => [
                                    'update' => function ($url, $model) {
                                        return Html::a(
                                            '<i class="fas fa-pencil-alt"></i>',
                                            ['update', 'id' => $model->id],
                                            [
                                                'class'               => 'btn btn-sm btn-success',
                                                'data-toggle'         => 'tooltip',
                                                'data-original-title' => 'Editar',
                                                'data-pjax'           => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a(
                                            '<i class="fas fa-trash-alt"></i>',
                                            ['delete', 'id' => $model->id],
                                            [
                                                'class'               => 'btn btn-sm btn-danger',
                                                'data'                => [
                                                    'confirm' => 'Tem certeza que deseja excluir este tipo de doação?',
                                                    'method'  => 'post',
                                                ],
                                                'data-toggle'         => 'tooltip',
                                                'data-original-title' => 'Excluir',
                                            ]
                                        );
                                    },
                                ],
                            ],
                        ],
                    ]); ?>

                </div>
            </div>
        </div>
    </div>
</div>