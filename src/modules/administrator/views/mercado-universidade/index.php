<?php

use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vincular Mercados a Universidades';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Vínculo de mercado', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Vínculo de mercado', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="p-3">
            <p>
                <?= Html::a('Vincular Mercado', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'pjax' => true,
                'hover' => true,
                'panel' => [
                    'heading' => '<i class="fa fa-store"></i> Lista de vínculos de mercados',
                    'before' => '<div style="padding-top: 7px;"><em></em></div>',
                ],
                'export' => ['fontAwesome' => true],
                'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],
                'columns' => [
                    ['class' => 'yii\\grid\\SerialColumn'],
                    [
                        'attribute' => 'id',
                        'label' => 'ID',
                        'vAlign' => 'middle',
                        'headerOptions' => ['style' => 'width:80px'],
                    ],
                    [
                        'label' => 'Mercado',
                        'value' => static function ($model) {
                            if ($model->mercado === null) {
                                return '-';
                            }

                            return $model->mercado->nome_mercado . ' — ' . $model->mercado->endereco;
                        },
                        'vAlign' => 'middle',
                    ],
                    [
                        'label' => 'Universidade',
                        'value' => static function ($model) {
                            return $model->universidade->nome ?? '-';
                        },
                        'vAlign' => 'middle',
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
