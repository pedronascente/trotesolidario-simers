<?php

use app\modules\common\models\Doacao;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Visualizar doação';
$this->params['breadcrumbs'][] = ['label' => 'Doacoes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doacao-view container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= Html::encode($this->title) ?></h1>
    </div>
  <?php if ($model->arquivo): ?>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="small text-muted mb-1">clique para ver o comprovante</div>
                <?= Html::a('Abrir comprovante atual', Yii::getAlias('@web') . '/imagens/doacoes/' . $model->arquivo, [
                    'target' => '_blank',
                    'data-pjax' => '0',
                ]) ?>
            </div>
        </div>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Tipo de doação',
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
                'value' => Doacao::getStatusList()[$model->status] ?? $model->status,
            ],
            'motivo_reprovado:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>
  
    <div class="d-flex align-items-center">
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-outline-secondary mr-2']) ?>
        <?php if (in_array($model->status, [Doacao::STATUS_PENDENTE, Doacao::STATUS_REJEITADA], true)): ?>
            <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>
</div>