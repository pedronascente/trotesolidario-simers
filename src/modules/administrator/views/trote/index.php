<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;
use yii\helpers\StringHelper;
use app\modules\common\models\Trote;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\TroteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trote';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<div class="container-fluid">

    <!-- Alerts -->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget([ 'type' => Alert::TYPE_SUCCESS, 'title' => 'Trote', 'icon' => 'fas fa-check-circle','body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000,]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget([ 'type' => Alert::TYPE_DANGER, 'title' => 'Trote', 'icon' => 'fas fa-times-circle',  'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000, ]) ?>
    <?php endif; ?>

    <!-- Header -->
    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title); ?></h1>
    </div> -->

    <div class="card shadow mb-4">
        <div class="p-3">

            <p>
                <?= Html::a('Criar Trote', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax' => true,
                'hover' => true,
                'panel' => [
                    'heading' => '<i class="fa fa-hand-holding-heart"></i> Lista de Trotes',
                    'before' => '<div style="padding-top: 7px;"><em></em></div>',
                ],
                'export' => ['fontAwesome' => true],
                'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],

                // Destaque visual
                'rowOptions' => function ($model) {
                    return $model->status === Trote::STATUS_ATIVO
                        ? ['class' => 'table-success']
                        : [];
                },
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'titulo',
                        'vAlign'    => 'middle',
                    ],
                    [
                        'attribute' => 'descricao',
                        'format'    => 'raw',
                        'value'     => fn($model) =>
                        StringHelper::truncate(strip_tags($model->descricao), 80),
                        'headerOptions' => ['style' => 'width:250px'],
                    ],
                    [
                        'label'  => 'Edição',
                        'value'  => fn($model) => $model->getEdicaoFormatada(),
                        'filter' => false,
                        'headerOptions' => ['style' => 'width:120px'],
                    ],
                    [
                        'attribute' => 'status',
                        'format'    => 'raw',
                        'value'     => function ($model) {
                            return match ($model->status) {
                                Trote::STATUS_ATIVO     => '<span class="badge badge-success">Ativo</span>',
                                Trote::STATUS_ENCERRADO => '<span class="badge badge-secondary">Encerrado</span>',
                                default                => '<span class="badge badge-warning">Rascunho</span>',
                            };
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter'     => Trote::getStatusList(),
                        'filterInputOptions' => ['placeholder' => 'Status'],
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'headerOptions' => ['style' => 'width:150px'],
                    ],
                    [
                        'label' => 'Período',
                        'value' => function ($model) {
                            if ($model->data_inicio && $model->data_fim) {
                                return Yii::$app->formatter->asDate($model->data_inicio)
                                    . ' até ' .
                                    Yii::$app->formatter->asDate($model->data_fim);
                            }
                            return '-';
                        },
                        'filter' => false,
                    ],
                    [
                        'class' => '\kartik\grid\ActionColumn',
                        'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                        'buttons' => [

                            'update' => fn($url) =>
                            Html::a(
                                '<i class="fas fa-pencil-alt"></i>',
                                $url,
                                [
                                    'class' => 'btn btn-sm btn-primary',
                                    'title' => 'Editar',
                                    'data-pjax' => '0',
                                ]
                            ),

                            'delete' => function ($url, $model) {
                                if ($model->status === Trote::STATUS_ATIVO) {
                                    return '';
                                }
                                return Html::a(
                                    '<i class="fas fa-trash"></i>',
                                    $url,
                                    [
                                        'class' => 'btn btn-sm btn-danger',
                                        'title' => 'Excluir',
                                        'data' => [
                                            'method'  => 'post',
                                            'confirm' => 'Deseja realmente excluir este trote?',
                                            'pjax'    => '0',
                                        ],
                                    ]
                                );
                            },
                        ],
                        'headerOptions' => ['style' => 'width:120px'],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>