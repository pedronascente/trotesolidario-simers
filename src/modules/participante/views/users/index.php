<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\common\models\Helper;
use app\modules\common\models\Users;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\UsersSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Usuários</h1>
    </div>
    <!-- Color System -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= Html::a('<i class="fas fa-plus mr-1" aria-hidden="true"></i> Criar usuário', ['create'], [
                            'class' => 'btn btn-success btn-sm',
                            'aria-label' => 'Criar usuário',
                        ]) ?>
                    </h6>
                </div>
                <div class="p-3">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            'name',
                            'email:email',
                            [
                                'attribute' => 'status',
                                'value' => function ($model) {
                                    return ($model->status == Users::STATUS_ACTIVE) ? "Ativo" : "Inativo";
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [1 => 'Ativo', 0 => 'Inativo'],
                                'filterInputOptions' => ['placeholder' => 'Status'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '{view} {update}',
                                'buttons' => [
                                    'view' => static function ($url, $model) {
                                        return Html::a('<i class="fas fa-eye" aria-hidden="true"></i>', $url, [
                                            'class' => 'btn btn-sm btn-outline-info',
                                            'title' => 'Visualizar usuário',
                                            'aria-label' => 'Visualizar ' . $model->name,
                                            'data-pjax' => '0',
                                        ]);
                                    },
                                    'update' => static function ($url, $model) {
                                        return Html::a('<i class="fas fa-pencil-alt" aria-hidden="true"></i>', $url, [
                                            'class' => 'btn btn-sm btn-outline-primary',
                                            'title' => 'Editar usuário',
                                            'aria-label' => 'Editar ' . $model->name,
                                            'data-pjax' => '0',
                                        ]);
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
