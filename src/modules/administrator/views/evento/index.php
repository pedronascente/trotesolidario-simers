<?php

use app\modules\common\models\Trote;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\EventoSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Evento';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Evento', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Evento', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3">
                    <p>
                        <?= Html::a('Criar Evento', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-calendar"></i> Lista de Eventos',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
                        'export' => ['fontAwesome' => true],
                        'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],
                        'columns' => [
                            ['class' => 'yii\\grid\\SerialColumn'],
                            'nome',
                            [
                                'attribute' => 'trote_id',
                                'label' => 'Trote',
                                'value' => fn($model) => $model->trote ? (($model->trote->titulo ?: 'Sem titulo') . ' | ' . ($model->trote->edicao ?: '-')) : '-',
                                'filter' => ArrayHelper::map(Trote::find()->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])->all(), 'id', function (Trote $trote) {
                                    return ($trote->titulo ?: 'Sem titulo') . ' | ' . ($trote->edicao ?: '-');
                                }),
                            ],
                            [
                                'attribute' => 'data_evento',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                                'filter' => false,
                            ],
                            [
                                'headerOptions' => ['style' => 'width:12%'],
                                'class' => '\\kartik\\grid\\ActionColumn',
                                'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        return Html::a('<i class="fas fa-pencil-alt"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-success', 'data-toggle' => 'tooltip', 'data-original-title' => 'Editar', 'data-pjax' => '0']);
                                    },
                                    'delete' => function ($url, $model) {
                                        return Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'id' => $model->id], ['class' => 'btn btn-sm btn-danger', 'data' => ['confirm' => 'Tem certeza que deseja excluir este evento?', 'method' => 'post'], 'data-toggle' => 'tooltip', 'data-original-title' => 'Excluir']);
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
