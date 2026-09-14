<?php

use app\modules\common\models\Trote;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\EventoSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eventos';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Evento', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Evento', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de eventos</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo evento', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
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
                            'class' => '\\kartik\\grid\\ActionColumn',
                            'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                            'headerOptions' => ['style' => 'width:120px'],
                            'buttons' => [
                                'update' => function ($url, $model) {
                                    return Html::a('<i class="fas fa-pencil-alt" aria-hidden="true"></i>', ['update', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-primary',
                                        'title' => 'Editar evento',
                                        'aria-label' => 'Editar ' . $model->nome,
                                        'data-pjax' => '0',
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    return Html::a('<i class="fas fa-trash-alt" aria-hidden="true"></i>', ['delete', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-danger',
                                        'title' => 'Excluir evento',
                                        'aria-label' => 'Excluir ' . $model->nome,
                                        'data' => [
                                            'confirm' => 'Deseja realmente excluir o evento "' . $model->nome . '"?',
                                            'method' => 'post',
                                            'pjax' => '0',
                                        ],
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
    </div>
</div>