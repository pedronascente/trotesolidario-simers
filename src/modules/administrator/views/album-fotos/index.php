<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Álbum de Fotos';
$this->params['breadcrumbs'][] = ['label' => 'Álbum de Fotos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<CSS
.album-fotos-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    white-space: nowrap;
}
CSS);
?>

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
                    'template' => '<div class="album-fotos-actions">{view}{update}{delete}</div>',
                    'headerOptions' => ['style' => 'width: 130px;'],
                    'buttons' => [
                        'view' => static fn($url) => Html::a('<i class="fas fa-eye"></i>', $url, ['class' => 'btn btn-sm btn-outline-info', 'data-pjax' => '0', 'title' => 'Visualizar', 'aria-label' => 'Visualizar']),
                        'update' => static fn($url) => Html::a('<i class="fas fa-pencil-alt"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => '0', 'title' => 'Editar', 'aria-label' => 'Editar']),
                        'delete' => static fn($url) => Html::a('<i class="fas fa-trash-alt"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'aria-label' => 'Excluir',
                            'title' => 'Excluir',
                            'data' => ['confirm' => 'Deseja realmente excluir esta foto?', 'method' => 'post'],
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
