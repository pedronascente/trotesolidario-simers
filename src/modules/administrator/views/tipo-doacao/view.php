<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\TipoDoacao */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Tipos Doação', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tipo-doacao-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Atualizar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Excluir', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Tem certeza que deseja excluir este item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            'descricao',
            'carga_horaria',
            'pontuacao_ranking',
            [
                'attribute' => 'ativo',
                'value' => $model->ativo ? 'Sim' : 'Não',
            ],
        ],
    ]) ?>

</div>