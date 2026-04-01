<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Doacao #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Doacoes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doacao-view container-fluid">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($model->status === \app\modules\common\models\Doacao::STATUS_PENDENTE): ?>
            <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Participacao',
                'value' => $model->getParticipacaoDisplay(),
            ],
            [
                'label' => 'Tipo de doacao',
                'value' => $model->tipoDoacao->nome ?? '-',
            ],
            [
                'label' => 'Evento',
                'value' => $model->evento->nome ?? '-',
            ],
            'cpf_snapshot',
            'edicao_snapshot',
            'arquivo',
            [
                'attribute' => 'status',
                'value' => \app\modules\common\models\Doacao::getStatusList()[$model->status] ?? $model->status,
            ],
            'motivo_reprovado:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
</div>
