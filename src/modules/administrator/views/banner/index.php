<?php

use app\modules\common\models\Banner;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\alert\Alert;

$this->title = 'Banner';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
    .banner-thumbnail { width: 140px; height: 70px; object-fit: cover; }
</style>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_SUCCESS,
            'title' => 'Banner',
            'icon' => 'fas fa-check-circle',
            'body' => Yii::$app->session->getFlash('success'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_DANGER,
            'title' => 'Banner',
            'icon' => 'fas fa-times-circle',
            'body' => Yii::$app->session->getFlash('error'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-success">Lista de banners</h6>
            <?php if ($podeCadastrar): ?>
                <?= Html::a('<i class="fas fa-plus mr-1"></i> Cadastrar banner', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
            <?php endif; ?>
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
                            [
                                'attribute' => 'tipo',
                                'label' => 'Local de exibição',
                                'filter' => Banner::getLocaisExibicao(),
                                'value' => static fn($model) => Banner::getLocaisExibicao()[$model->tipo] ?? $model->tipo,
                                'vAlign' => 'middle',
                            ],
                            [
                                'format' => 'raw',
                                'filter' => false,
                                'attribute' => 'img_dsk',
                                'label' => 'Imagem desktop',
                                'value' => function ($model) {
                                    $localExibicao = Banner::getLocaisExibicao()[$model->tipo] ?? $model->tipo;

                                    return $model->img_dsk
                                        ? Html::img(Yii::$app->getUrlManager()->getBaseUrl() . '/img/' . basename($model->img_dsk), [
                                            'class' => 'img-thumbnail banner-thumbnail',
                                            'alt' => 'Banner desktop - ' . $localExibicao,
                                        ])
                                        : '-';
                                },
                            ],
                            [
                                'format' => 'raw',
                                'filter' => false,
                                'attribute' => 'img_mob',
                                'label' => 'Imagem mobile',
                                'value' => function ($model) {
                                    $localExibicao = Banner::getLocaisExibicao()[$model->tipo] ?? $model->tipo;

                                    return $model->img_mob
                                        ? Html::img(Yii::$app->getUrlManager()->getBaseUrl() . '/img/' . basename($model->img_mob), [
                                            'class' => 'img-thumbnail banner-thumbnail',
                                            'alt' => 'Banner mobile - ' . $localExibicao,
                                        ])
                                        : '-';
                                },
                            ],
                            [
                                'format' => 'raw',
                                'attribute' => 'ativo',
                                'label' => 'Ativo',
                                'filter' => [1 => 'Sim', 0 => 'Não'],
                                'hAlign' => 'center',
                                'vAlign' => 'middle',
                                'value' => function ($model) {
                                    return Html::tag('span', $model->ativo === 1 ? 'Sim' : 'Não', [
                                        'class' => 'badge badge-' . ($model->ativo === 1 ? 'success' : 'secondary'),
                                    ]);
                                },
                            ],
                            [
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '<div class="btn-group-actions">{update} {toggle} {delete}</div>',
                                'headerOptions' => ['style' => 'width:160px'],
                                'visibleButtons' => [
                                    'delete' => static fn($model) => (int) $model->ativo !== 1,
                                ],
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        $localExibicao = Banner::getLocaisExibicao()[$model->tipo] ?? $model->tipo;

                                        return Html::a(
                                            '<i class="fas fa-pencil-alt" aria-hidden="true"></i>',
                                            ['update', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm btn-primary',
                                                'title' => 'Editar banner',
                                                'aria-label' => 'Editar banner - ' . $localExibicao,
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'toggle' => function ($url, $model) {
                                        $active = (int) $model->ativo === 1;
                                        $localExibicao = Banner::getLocaisExibicao()[$model->tipo] ?? $model->tipo;

                                        return Html::a(
                                            '<i class="fas ' . ($active ? 'fa-ban' : 'fa-check') . '" aria-hidden="true"></i>',
                                            ['toggle', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm ' . ($active ? 'btn-warning' : 'btn-success'),
                                                'title' => $active ? 'Desativar banner' : 'Ativar banner',
                                                'aria-label' => ($active ? 'Desativar banner - ' : 'Ativar banner - ') . $localExibicao,
                                                'data' => [
                                                    'confirm' => $active ? 'Deseja desativar este banner?' : 'Deseja ativar este banner e desativar os demais do mesmo local de exibição?',
                                                    'method' => 'post',
                                                    'pjax' => '0',
                                                ],
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model) {
                                        $localExibicao = Banner::getLocaisExibicao()[$model->tipo] ?? $model->tipo;

                                        return Html::a(
                                            '<i class="fas fa-trash-alt" aria-hidden="true"></i>',
                                            ['delete', 'id' => $model->id],
                                            [
                                                'class' => 'btn btn-sm btn-danger',
                                                'title' => 'Excluir banner',
                                                'aria-label' => 'Excluir banner - ' . $localExibicao,
                                                'data' => [
                                                    'confirm' => 'Deseja realmente excluir este banner?',
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
</div>
