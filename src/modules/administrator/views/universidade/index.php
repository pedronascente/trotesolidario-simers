<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;
 
$this->title = 'Universidades';
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

<div class="container-fluid">

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Universidade', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000,]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Universidade', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000,]) ?>
    <?php endif; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> Universidades</h1>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="p-3">
                    <p>
                        <?= Html::a('Criar universidade', ['create'], ['class' => 'btn btn-success']) ?>
                    </p>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'hover' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-university"></i> Universidades',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
                        'export' => ['fontAwesome' => true],
                        'exportConfig' => ['html' => [], 'csv' => [], 'txt' => [], 'xls' => [], 'json' => []],
                        'columns' => [

                            'nome',
                            [
                                'attribute' => 'cidade',
                                'label' => 'Cidade',
                                'filterInputOptions' => [
                                    'class' => 'form-control',
                                    'placeholder' => 'Pesquisar cidade...'
                                ],
                            ],
                            [
                                'attribute' => 'estado',
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
                                'hiddenFromExport' => true,
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

                            // Ações
                            [
                                'headerOptions' => ['style' => 'width:10%'],
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                                'buttons' => [
                                    'update' => function ($url) {
                                        return Html::a('<i class="fas fa-pencil-alt"></i>', $url, [
                                            'class' => 'btn btn-sm btn-primary',
                                            'title' => 'Editar',
                                            'data-pjax' => '0',
                                        ]);
                                    },
                                    'delete' => function ($url, $model) {
                                        $ativo = $model->ativo == 1;
                                        return Html::a(
                                            '<i class="fa ' . ($ativo ? 'fa-ban' : 'fa-check') . '"></i>',
                                            $url,
                                            [
                                                'class' => 'btn btn-sm ' . ($ativo ? 'btn-danger' : 'btn-success'),
                                                'title' => $ativo ? 'Desativar' : 'Ativar',
                                                'data-method' => 'post',
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