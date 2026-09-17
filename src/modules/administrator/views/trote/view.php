<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Trote */

$this->title = $model->titulo ?: $model->edicao;
$this->params['breadcrumbs'][] = ['label' => 'Trotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trote-view container-fluid">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'titulo',
            'edicao',
            [
                'attribute' => 'descricao',
                'format' => 'ntext',
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel(),
            ],
            'data_inicio:date',
            'data_fim:date',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        <?php if ($model->status !== \app\modules\common\models\Trote::STATUS_ATIVO): ?>
            <?= Html::a('Excluir', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-outline-danger',
                'data' => [
                    'confirm' => 'Deseja realmente excluir este item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>
</div>
