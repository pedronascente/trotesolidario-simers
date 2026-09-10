<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Álbum de Fotos';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <?php foreach (['success' => 'success', 'error' => 'danger'] as $flash => $type): ?>
        <?php if (Yii::$app->session->hasFlash($flash)): ?>
            <div class="alert alert-<?= $type ?> alert-dismissible fade show" role="alert">
                <?= Html::encode(Yii::$app->session->getFlash($flash)) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-success">Fotos dos participantes</h6>
            <?= Html::a('<i class="fas fa-plus mr-1"></i> Nova foto', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
        </div>
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax' => true,
                'hover' => true,
                'responsive' => true,
                'columns' => [
                    ['attribute' => 'id', 'width' => '70px'],
                    [
                        'label' => 'Imagem',
                        'format' => 'raw',
                        'filter' => false,
                        'value' => static fn($model) => Html::img(['arquivo', 'id' => $model->id], [
                            'class' => 'img-thumbnail',
                            'style' => 'width: 100px; height: 70px; object-fit: cover;',
                            'alt' => $model->titulo,
                        ]),
                    ],
                    'titulo',
                    [
                        'attribute' => 'participante',
                        'value' => static fn($model) => $model->participacao->user->nome ?? '-',
                    ],
                    [
                        'attribute' => 'trote',
                        'label' => 'Trote / edição',
                        'value' => static function ($model) {
                            $trote = $model->participacao->trote ?? null;
                            return $trote ? $trote->titulo . ' | ' . $trote->edicao : '-';
                        },
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => ['datetime', 'php:d/m/Y H:i'],
                        'filter' => false,
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => static fn($url) => Html::a('<i class="fas fa-eye"></i>', $url, ['class' => 'btn btn-info btn-sm', 'data-pjax' => '0', 'title' => 'Visualizar']),
                            'update' => static fn($url) => Html::a('<i class="fas fa-pencil-alt"></i>', $url, ['class' => 'btn btn-success btn-sm', 'data-pjax' => '0', 'title' => 'Editar']),
                            'delete' => static fn($url) => Html::a('<i class="fas fa-trash-alt"></i>', $url, [
                                'class' => 'btn btn-danger btn-sm',
                                'title' => 'Excluir',
                                'data' => ['confirm' => 'Deseja realmente excluir esta foto?', 'method' => 'post'],
                            ]),
                        ],
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>
