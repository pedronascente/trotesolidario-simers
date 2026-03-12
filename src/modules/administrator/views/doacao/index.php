<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;

$this->title = 'Doação';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions {   display: flex;   gap: 6px;   justify-content: center; align-items: center;}
</style>

<div class="container-fluid">

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Informativo', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000,]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Informativo', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000,]) ?>
    <?php endif; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> <?= Html::encode($this->title); ?></h1>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3">
                    <p>
                        <?= Html::a('Criar Doação', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-hand-holding-heart"></i> Lista de Doações',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
                        'export' => false,
                        'toolbar' => false,
                        'columns' => [
                            [
                                'headerOptions' => ['style' => 'width:5%'],
                                'format' => 'raw',
                                'attribute' => 'arquivo',
                                'label' => 'Arquivo',
                                'value' => function ($model) {
                                    return $model->arquivo
                                        ? Html::img(
                                            Yii::getAlias('@imgArquivosDoacao') .'/'. $model->arquivo,
                                            [
                                                'class' => 'img-thumbnail',
                                                'style' => 'max-width:60px; height:auto;'
                                            ]
                                        )
                                        : '-';
                                },
                                'hiddenFromExport' => true,
                            ],
                            [
                                'attribute' => 'user_id',
                                'label' => 'Usuário',
                                'value' => function ($model) {
                                    return $model->user->name ?? '-';
                                },
                                'filterInputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Pesquisar usuário...'
                                ],
                            ],
                            [
                                'attribute' => 'universidade_nome',
                                'label' => 'Universidade',
                                'value' => function ($model) {
                                    return $model->universidade->nome ?? '-';
                                },
                                'filterInputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Pesquisar universidade...'
                                ],
                            ],
                            [
                                'attribute' => 'trote_titulo',
                                'label' => 'Trote',
                                'value' => function ($model) {
                                    return $model->trote->titulo ?? '-';
                                },
                                'filterInputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Pesquisar trote...'
                                ],
                            ],
                            [
                                'attribute' => 'evento_nome',
                                'label' => 'Evento',
                                'value' => function ($model) {
                                    return $model->evento->nome ?? '-';
                                },
                                'filterInputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Pesquisar evento...'
                                ],
                            ],
                            [
                                'attribute' => 'tipo_doacao_id',
                                'label' => 'Tipo Doação',
                                'value' => function ($model) {
                                    return $model->tipoDoacao->nome ?? '-';
                                },
                                'filterInputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Tipo de doação...'
                                ],
                            ],
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'attribute' => 'status',
                                'label' => 'Status',
                                'value' => function ($model) {

                                    switch ($model->status) {
                                        case 'aprovado':
                                            return 'Aprovado';

                                        case 'pendente':
                                            return 'Pendente';

                                        case 'rejeitado':
                                            return 'rejeitado';

                                        default:
                                            return '-';
                                    }
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    'aprovado' => 'Aprovado',
                                    'pendente' => 'Pendente',
                                    'rejeitado' => 'Rejeitado'
                                ],
                                'filterInputOptions' => ['placeholder' => 'Status'],
                                'filterWidgetOptions' => [
                                    'pluginOptions' => ['allowClear' => true]
                                ],
                            ],

                            [
                                'headerOptions' => ['style' => 'width:15%'],
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '<div class="btn-group-actions">{view} {aprovar} {reprovar}</div>',
                                'buttons' => [

                                    'view' => function ($url, $model) {
                                        return Html::a(
                                            '<i class="fa fa-eye"></i>',
                                            ['view', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm btn-info',
                                                'title' => 'Visualizar',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },

                                    'aprovar' => function ($url, $model) {

                                        if ($model->status === 'aprovado') {
                                            return '';
                                        }
                                        return Html::a(
                                            '<i class="fa fa-check"></i>',
                                            ['aprovar', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm btn-success',
                                                'title' => 'Aprovar',
                                                'data-method' => 'post',
                                                'data-confirm' => 'Confirmar aprovação?',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },

                                    'reprovar' => function ($url, $model) {
                                        if ($model->status === 'rejeitado') {
                                            return '';
                                        }
                                        return Html::a(
                                            '<i class="fa fa-times"></i>',
                                            ['reprovar', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm btn-danger',
                                                'title' => 'Rejeitar',
                                                'data-method' => 'post',
                                                'data-confirm' => 'Confirmar reprovação?',
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