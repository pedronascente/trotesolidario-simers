<?php

use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mercados parceiros';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Mercado parceiro', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Mercado parceiro', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de mercados parceiros</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo mercado', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'hover' => true,
            'responsive' => true,
            'columns' => [
                ['class' => 'yii\\grid\\SerialColumn'],
                ['attribute' => 'nome_mercado', 'vAlign' => 'middle'],
                ['attribute' => 'endereco', 'vAlign' => 'middle'],
                ['attribute' => 'numero', 'vAlign' => 'middle'],
                ['attribute' => 'bairro', 'vAlign' => 'middle'],
                [
                    'class' => '\\kartik\\grid\\ActionColumn',
                    'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                    'headerOptions' => ['style' => 'width:120px'],
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a(
                                '<i class="fas fa-pencil-alt" aria-hidden="true"></i>',
                                ['update', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-sm btn-primary',
                                    'title' => 'Editar mercado',
                                    'aria-label' => 'Editar ' . $model->nome_mercado,
                                    'data-pjax' => '0',
                                ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<i class="fas fa-trash-alt" aria-hidden="true"></i>',
                                ['delete', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-sm btn-danger',
                                    'title' => 'Excluir mercado',
                                    'aria-label' => 'Excluir ' . $model->nome_mercado,
                                    'data' => [
                                        'confirm' => 'Deseja realmente excluir o mercado "' . $model->nome_mercado . '"?',
                                        'method' => 'post',
                                        'pjax' => '0',
                                    ],
                                ]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>