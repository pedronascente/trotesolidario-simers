<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;

$this->title = 'Informativo';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex;  gap: 6px; justify-content: center; align-items: center; }
</style>

<div class="container-fluid">

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Informativo', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000,]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Informativo', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000,]) ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3">
                    <p>
                        <?= Html::a('Criar Informativo', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'headerContainer' => ['style' => 'top:50px', 'class' => 'kv-table-header'],
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-hand-holding-heart"></i> Lista de Informativos',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
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
                        'columns' => [
                            'nome',
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'format' => 'raw',
                                'filter' => false,
                                'attribute' => 'arquivo',
                                'label' => 'Icon',
                                'value' => function ($model) {
                                    if ($model->arquivo) {
                                        return Html::a(
                                            'Link do arquivo',
                                            Yii::$app->request->baseUrl . '/pdf/' . $model->arquivo,
                                            ['target' => '_blank']
                                        );
                                    }
                                    return null;
                                },
                                'hiddenFromExport' => true,
                            ],
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                                'buttons' => [
                                    'update' => function ($url) {
                                        return Html::a(
                                            '<i class="fas fa-pencil-alt"></i>',
                                            $url,
                                            [
                                                'class' => 'btn btn-small btn-success',
                                                'data-toggle' => 'tooltip',
                                                'data-original-title' => 'Editar',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a(
                                            '<i class="fas fa-trash-alt"></i>',
                                            ['delete', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-small btn-danger',
                                                'data' => [
                                                    'confirm' => 'Tem certeza que deseja excluir este informativo?',
                                                    'method' => 'post',
                                                ],
                                                'data-toggle' => 'tooltip',
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