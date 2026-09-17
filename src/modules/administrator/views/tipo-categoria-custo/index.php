<?php
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;
$this->title = 'Categorias de custos';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php foreach (['success' => Alert::TYPE_SUCCESS, 'error' => Alert::TYPE_DANGER] as $key => $type): ?>
<?php if (Yii::$app->session->hasFlash($key)): ?><?= Alert::widget(['type' => $type, 'body' => Html::encode(Yii::$app->session->getFlash($key)), 'delay' => 4000]) ?><?php endif; ?>
<?php endforeach; ?>
<div class="card shadow mb-4">
<div class="card-header py-3 d-flex justify-content-between align-items-center"><h6 class="m-0 font-weight-bold text-success">Categorias de custos</h6><?= Html::a('<i class="fas fa-plus mr-1"></i> Nova categoria', ['create'], ['class' => 'btn btn-success btn-sm']) ?></div>
<div class="card-body"><?= GridView::widget([
    'dataProvider' => $dataProvider, 'filterModel' => $searchModel, 'responsive' => true, 'hover' => true,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'], 'nome', 'descricao',
        ['attribute' => 'ativo', 'filter' => [1 => 'Sim', 0 => 'Não'], 'format' => 'raw', 'value' => static fn($m) => $m->ativo ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>'],
        ['class' => 'kartik\grid\ActionColumn', 'template' => '{update} {delete}', 'buttons' => [
            'update' => static fn($url, $m) => Html::a('<i class="fas fa-pencil-alt"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-primary', 'title' => 'Editar', 'aria-label' => 'Editar ' . $m->nome]),
            'delete' => static fn($url, $m) => Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-danger', 'title' => 'Excluir', 'aria-label' => 'Excluir ' . $m->nome, 'data' => ['method' => 'post', 'confirm' => 'Deseja excluir esta categoria?']]),
        ]],
    ],
]) ?></div></div>
