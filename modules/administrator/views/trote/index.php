<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\participante\models\Trote;
use yii\helpers\ArrayHelper;
use kartik\alert\Alert;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\participante\models\UniversidadeSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Universidades';
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

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_SUCCESS,
            'title' => 'Trote',
            'icon' => 'fas fa-check-circle',
            'body' => Yii::$app->session->getFlash('success'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_DANGER,
            'title' => 'Trote',
            'icon' => 'fas fa-times-circle',
            'body' => Yii::$app->session->getFlash('error'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> Trote</h1>
    </div>
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3"> 
                    <p>
                        <?= Html::a('Criar Trote', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'headerContainer' => ['style' => 'top:50px', 'class' => 'kv-table-header'],
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-book"></i>  Trote',
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
                            ['class' => 'yii\grid\SerialColumn'],
                            'nome',
                            'frase_certificado',
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'attribute' => 'ativo',
                                'value' => function ($model) {
                                    return ($model->ativo) ? "Sim" : "Não";
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [1 => 'Ativo', 0 => 'Inativo'],
                                'filterInputOptions' => ['placeholder' => 'Status'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                                'buttons' => [
                                    'update' => function ($url) {
                                        return Html::a('<i class="fas fa-pencil-alt"></i>', $url, [
                                            'class' => 'btn btn-sm btn-primary',
                                            'title' => 'Editar',
                                            'data-pjax' => '0',
                                        ]);
                                    },
                                    'delete' => function ($url, $model) {
                                        $ativo = $model->ativo == 1;
                                        return Html::a(
                                            '<i class="fa ' . ($ativo ? 'fa-ban' : 'fa-check') . '"></i>',
                                            $url,
                                            [
                                                'class' => 'btn btn-sm ' . ($ativo ? 'btn-danger' : 'btn-success'),
                                                'title' => $ativo ? 'Desativar' : 'Ativar',
                                                'data-method' => 'post',
                                                'data-pjax' => '0',
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