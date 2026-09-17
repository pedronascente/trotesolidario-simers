<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->titulo;
$this->params['breadcrumbs'][] = ['label' => 'Álbum de Fotos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6>
        <div>
            <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            <?= Html::a('Excluir', ['delete', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-danger', 'data' => ['confirm' => 'Deseja realmente excluir esta foto?', 'method' => 'post']]) ?>
        </div>
    </div>
    <div class="card-body">
        <div class="text-center mb-4">
            <?= Html::img(['arquivo', 'id' => $model->id], ['class' => 'img-fluid img-thumbnail', 'style' => 'max-height: 520px;', 'alt' => $model->titulo]) ?>
        </div>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'titulo',
                ['label' => 'Participante', 'value' => $model->participacao->user->nome ?? '-'],
                ['label' => 'Trote', 'value' => ($model->participacao->trote->titulo ?? '-') . ' | ' . ($model->participacao->trote->edicao ?? '-')],
                ['label' => 'Universidade', 'value' => $model->participacao->universidade->nome ?? '-'],
                ['attribute' => 'created_at', 'format' => ['datetime', 'php:d/m/Y H:i']],
                ['attribute' => 'updated_at', 'format' => ['datetime', 'php:d/m/Y H:i']],
            ],
        ]) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
</div>
