<?php

use app\models\User;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Participacoes';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Participacoes', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Participacoes', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3">
                    <p>
                        <?= Html::a('Criar participacao', ['create'], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Solicitacoes de correcao', ['solicitacoes-correcao-universidade'], ['class' => 'btn btn-outline-warning']) ?>
                    </p>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-user-plus"></i> Lista de Participacoes',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
                        'export' => ['fontAwesome' => true],
                        'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],
                        'columns' => [
                            ['class' => 'yii\\grid\\SerialColumn'],
                            [
                                'attribute' => 'user_nome',
                                'label' => 'Usuario',
                                'value' => static fn($model) => $model->user ? $model->user->nome : '-',
                                'filter' => ArrayHelper::map(User::find()->orderBy(['nome' => SORT_ASC])->all(), 'nome', 'nome'),
                            ],
                            [
                                'attribute' => 'user_email',
                                'label' => 'E-mail',
                                'value' => static fn($model) => $model->user->email ?? '-',
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'trote_edicao',
                                'label' => 'Trote',
                                'value' => static fn($model) => $model->trote ? (($model->trote->titulo ?: 'Sem titulo') . ' | ' . ($model->trote->edicao ?: '-')) : '-',
                                'filter' => ArrayHelper::map(Trote::find()->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])->all(), 'edicao', 'edicao'),
                            ],
                            [
                                'attribute' => 'universidade_nome',
                                'label' => 'Universidade',
                                'value' => static fn($model) => $model->universidade->nome ?? '-',
                                'filter' => ArrayHelper::map(Universidade::find()->orderBy(['nome' => SORT_ASC])->all(), 'nome', 'nome'),
                            ],
                            'curso',
                           

                             [
                                'attribute' => 'status',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return $model->getStatusBadge();
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => \app\modules\common\models\Participacao::getStatusList(),
                                'filterInputOptions' => ['placeholder' => 'Status'],
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true],
                                ],
                            ],




                            [
                                'attribute' => 'created_at',
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
                                        return Html::a('<i class="fas fa-trash-alt"></i>', ['delete', 'id' => $model->id], ['class' => 'btn btn-sm btn-danger', 'data' => ['confirm' => 'Tem certeza que deseja excluir esta participacao?', 'method' => 'post'], 'data-toggle' => 'tooltip', 'data-original-title' => 'Excluir']);
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
