<?php

use app\modules\common\models\Trote;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\common\models\TroteSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Trotes';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Trote', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Trote', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de trotes</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo trote', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'hover' => true,
            'responsive' => true,
            'rowOptions' => function ($model) {
                return $model->status === Trote::STATUS_ATIVO ? ['class' => 'table-success'] : [];
            },
            'columns' => [
                ['class' => 'yii\\grid\\SerialColumn'],
                [
                    'attribute' => 'titulo',
                    'vAlign' => 'middle',
                ],
                [
                    'attribute' => 'descricao',
                    'format' => 'raw',
                    'value' => fn($model) => StringHelper::truncate(strip_tags((string) $model->descricao), 80),
                    'headerOptions' => ['style' => 'width:250px'],
                ],
                [
                    'attribute' => 'edicao',
                    'headerOptions' => ['style' => 'width:120px'],
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return match ($model->status) {
                            Trote::STATUS_ATIVO => '<span class="badge badge-success">Ativo</span>',
                            Trote::STATUS_ENCERRADO => '<span class="badge badge-secondary">Encerrado</span>',
                            default => '<span class="badge badge-warning">Rascunho</span>',
                        };
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => Trote::getStatusList(),
                    'filterInputOptions' => ['placeholder' => 'Status'],
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                    'headerOptions' => ['style' => 'width:150px'],
                ],
                [
                    'label' => 'Periodo',
                    'value' => function ($model) {
                        if ($model->data_inicio && $model->data_fim) {
                            return Yii::$app->formatter->asDate($model->data_inicio) . ' ate ' . Yii::$app->formatter->asDate($model->data_fim);
                        }

                        if ($model->data_inicio) {
                            return 'Inicio em ' . Yii::$app->formatter->asDate($model->data_inicio);
                        }

                        if ($model->data_fim) {
                            return 'Fim em ' . Yii::$app->formatter->asDate($model->data_fim);
                        }

                        return '-';
                    },
                    'filter' => false,
                ],
                [
                    'class' => '\\kartik\\grid\\ActionColumn',
                    'template' => '<div class="btn-group-actions">{view} {update} {delete}</div>',
                    'buttons' => [
                        'view' => fn($url, $model) => Html::a('<i class="fas fa-eye" aria-hidden="true"></i>', $url, [
                            'class' => 'btn btn-sm btn-info',
                            'title' => 'Visualizar trote',
                            'aria-label' => 'Visualizar ' . $model->titulo,
                            'data-pjax' => '0',
                        ]),
                        'update' => fn($url, $model) => Html::a('<i class="fas fa-pencil-alt" aria-hidden="true"></i>', $url, [
                            'class' => 'btn btn-sm btn-primary',
                            'title' => 'Editar trote',
                            'aria-label' => 'Editar ' . $model->titulo,
                            'data-pjax' => '0',
                        ]),
                        'delete' => function ($url, $model) {
                            if ($model->status === Trote::STATUS_ATIVO) {
                                return '';
                            }

                            return Html::a('<i class="fas fa-trash" aria-hidden="true"></i>', $url, [
                                'class' => 'btn btn-sm btn-danger',
                                'title' => 'Excluir trote',
                                'aria-label' => 'Excluir ' . $model->titulo,
                                'data' => [
                                    'method' => 'post',
                                    'confirm' => 'Deseja realmente excluir o trote "' . $model->titulo . '"?',
                                    'pjax' => '0',
                                ],
                            ]);
                        },
                    ],
                    'headerOptions' => ['style' => 'width:160px'],
                ],
            ],
        ]); ?>
    </div>
</div>