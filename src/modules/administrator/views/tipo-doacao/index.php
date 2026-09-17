<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\TipoDoacaoSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipos de doação';
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
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'title' => 'Tipo de doação',
        'icon' => 'fas fa-check-circle',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => 4000,
    ]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Tipo de doação',
        'icon' => 'fas fa-times-circle',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 4000,
    ]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de tipos de doação</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo tipo de doação', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'hover' => true,
            'responsive' => true,
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
                    'class'         => '\kartik\grid\ActionColumn',
                    'template'      => '<div class="btn-group-actions">{update} {delete}</div>',
                    'headerOptions' => ['style' => 'width:120px'],
                    'buttons'       => [
                        'update' => function ($url, $model) {
                            return Html::a(
                                '<i class="fas fa-pencil-alt" aria-hidden="true"></i>',
                                ['update', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'Editar tipo de doação',
                                    'aria-label' => 'Editar ' . $model->nome,
                                    'data-pjax' => '0',
                                ]
                            );
                        },
                        'delete' => function ($url, $model) {
                            return Html::a(
                                '<i class="fas fa-trash-alt" aria-hidden="true"></i>',
                                ['delete', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'title' => 'Excluir tipo de doação',
                                    'aria-label' => 'Excluir ' . $model->nome,
                                    'data' => [
                                        'confirm' => 'Deseja realmente excluir o tipo de doação "' . $model->nome . '"?',
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
