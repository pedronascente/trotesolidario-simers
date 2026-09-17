<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;

$this->title = 'Universidade';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Universidade', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000,]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Universidade', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000,]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de universidades</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Nova universidade', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
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
                        ['attribute' => 'nome', 'vAlign' => 'middle'],
                        [
                            'attribute' => 'cidade',
                            'label' => 'Cidade',
                            'filterInputOptions' => [
                                'class' => 'form-control',
                                'placeholder' => 'Pesquisar cidade...'
                            ],
                        ],
                        [
                            'attribute' => 'uf',
                            'label' => 'UF',
                            'value' => function ($model) {
                                return strtoupper($model->uf);
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => [
                                'AC' => 'AC',
                                'AL' => 'AL',
                                'AP' => 'AP',
                                'AM' => 'AM',
                                'BA' => 'BA',
                                'CE' => 'CE',
                                'DF' => 'DF',
                                'ES' => 'ES',
                                'GO' => 'GO',
                                'MA' => 'MA',
                                'MT' => 'MT',
                                'MS' => 'MS',
                                'MG' => 'MG',
                                'PA' => 'PA',
                                'PB' => 'PB',
                                'PR' => 'PR',
                                'PE' => 'PE',
                                'PI' => 'PI',
                                'RJ' => 'RJ',
                                'RN' => 'RN',
                                'RS' => 'RS',
                                'RO' => 'RO',
                                'RR' => 'RR',
                                'SC' => 'SC',
                                'SP' => 'SP',
                                'SE' => 'SE',
                                'TO' => 'TO',
                            ],
                            'filterInputOptions' => ['placeholder' => 'UF'],
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true],
                            ],
                        ],
                        'link_doacao_alimento:url',
                        [
                            'headerOptions' => ['style' => 'width:5%'],
                            'format' => 'raw',
                            'attribute' => 'icon',
                            'label' => 'Icon',
                            'value' => function ($model) {
                                return $model->icon
                                    ? Html::img(
                                        Yii::$app->getUrlManager()->getBaseUrl() . '/img/' . $model->icon,
                                        [
                                            'class' => 'img-thumbnail',
                                            'style' => 'max-width:60px; height:auto;'
                                        ]
                                    )
                                    : '-';
                            },
                        ],

                        [
                            'headerOptions' => ['style' => 'width:10%'],
                            'attribute' => 'ativo',
                            'label' => 'Ativo',
                            'value' => function ($model) {
                                return ($model->ativo) ? "Sim" : "Não";
                            },
                            'filterType' => GridView::FILTER_SELECT2,
                            'filter' => [1 => 'Ativo', 0 => 'Inativo'],
                            'filterInputOptions' => ['placeholder' => 'Status'],
                            'filterWidgetOptions' => [
                                'pluginOptions' => ['allowClear' => true]
                            ],
                        ],

                        [
                            'class' => '\kartik\grid\ActionColumn',
                            'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                            'headerOptions' => ['style' => 'width:120px'],
                            'buttons' => [
                                'update' => function ($url, $model) {
                                    return Html::a(
                                        '<i class="fas fa-pencil-alt" aria-hidden="true"></i>',
                                        ['update', 'id' => $model->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-primary',
                                            'title' => 'Editar universidade',
                                            'aria-label' => 'Editar ' . $model->nome,
                                            'data-pjax' => '0',
                                        ]
                                    );
                                },
                                'delete' => function ($url, $model) {
                                    $ativo = $model->ativo == 1;
                                    return Html::a(
                                        '<i class="fa ' . ($ativo ? 'fa-ban' : 'fa-check') . '" aria-hidden="true"></i>',
                                        ['delete', 'id' => $model->id],
                                        [
                                            'class' => 'btn btn-sm ' . ($ativo ? 'btn-outline-danger' : 'btn-outline-success'),
                                            'title' => $ativo ? 'Desativar universidade' : 'Ativar universidade',
                                            'aria-label' => ($ativo ? 'Desativar ' : 'Ativar ') . $model->nome,
                                            'data' => [
                                                'confirm' => ($ativo ? 'Deseja desativar a universidade "' : 'Deseja ativar a universidade "') . $model->nome . '"?',
                                                'method' => 'post',
                                                'pjax' => '0',
                                            ],
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
