<?php

use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Comissões organizadoras';
$this->params['breadcrumbs'][] = $this->title;
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
        <h6 class="m-0 font-weight-bold text-success">Membros publicados por instituição</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo membro', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <p class="text-muted">Somente membros marcados como publicados aparecem na página da instituição para os participantes.</p>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'hover' => true,
            'responsive' => true,
            'columns' => [
                ['attribute' => 'id', 'width' => '70px'],
                'nome',
                'cargo',
                [
                    'attribute' => 'universidade_id',
                    'filter' => false,
                    'value' => static fn($model) => $model->universidade->nome ?? '-',
                ],
                ['attribute' => 'ordem', 'width' => '110px'],
                [
                    'attribute' => 'ativo',
                    'filter' => [1 => 'Sim', 0 => 'Não'],
                    'format' => 'raw',
                    'value' => static fn($model) => Html::tag('span', $model->ativo ? 'Sim' : 'Não', [
                        'class' => 'badge badge-' . ($model->ativo ? 'success' : 'secondary'),
                    ]),
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => static fn($url) => Html::a('<i class="fas fa-pencil-alt"></i>', $url, ['class' => 'btn btn-sm btn-outline-primary', 'data-pjax' => '0', 'title' => 'Editar', 'aria-label' => 'Editar']),
                        'delete' => static fn($url) => Html::a('<i class="fas fa-trash-alt"></i>', $url, [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Excluir',
                            'aria-label' => 'Excluir',
                            'data' => ['confirm' => 'Deseja remover este membro da comissão?', 'method' => 'post'],
                        ]),
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
