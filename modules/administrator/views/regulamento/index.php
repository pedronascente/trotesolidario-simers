<?php

use app\modules\participante\models\Users;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\participante\models\Trote;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\participante\models\UniversidadeSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Regulamento';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> Regulamento</h1>
    </div>
    <!-- Color System -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="p-3">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'headerContainer' => ['style' => 'top:50px', 'class' => 'kv-table-header'], // offset from top
                        //'floatHeader' => true, // table header floats when you scroll
                        //'floatPageSummary' => true, // table page summary floats when you scroll
                        //'floatFooter' => false, // disable floating of table footer
                        //'pjax' => true, // pjax is set to always false for this demo
                        // parameters from the demo form
                        //  'responsive' => true,
                        //'bordered' => true,
                        //'striped' => true,
                        //'condensed' => true,
                        'hover' => true,
                        //'showPageSummary' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-book"></i>  Regulamento',
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
                        'columns' => [

                            'nome',
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'format' => 'raw',
                                'filter' => false,
                                'attribute' => 'arquivo',
                                'label' => 'Icon',
                                'value' => function ($model) {
                                    return Html::a('Link do arquivo', Yii::$app->getUrlManager()->getBaseUrl() . '/pdf/' . $model->arquivo, ['target' => '_blank']);
                                },
                                'hiddenFromExport' => true,
                            ],
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '{update}',
                                //'template' => '{view} {update} {download}',
                                'buttons' => [
                                    /*'view' => function ($url, $model) {
                                        return Html::a(

                                            '<i class="fa fa-eye" aria-hidden="true"></i>',
                                            $url,
                                            [
                                                'class' => 'btn btn-small btn-dark',
                                                'data-toggle' => 'tooltip',
                                                'data-method' => "post",
                                                'data-original-title' => 'Excluir',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },*/
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
                                    /*'delete' => function ($url, $model) {
                                        return Html::a(

                                            '<i class="fa ' . (($model->ativo == 1) ? 'fa-trash' : 'fa-reply') . '" aria-hidden="true"></i>',
                                            $url,
                                            [
                                                'class' => 'btn btn-small ' . (($model->ativo == 1) ? 'btn-danger' : 'btn-warning'),
                                                'data-toggle' => 'tooltip',
                                                'data-method' => "post",
                                                'data-original-title' => 'Excluir',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },*/
                                ],
                            ],
                        ],
                    ]); ?>

                </div>
            </div>
        </div>
    </div>
</div>