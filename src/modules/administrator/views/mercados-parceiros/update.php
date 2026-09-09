<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\MercadoParceiro */

$this->title = 'Atualizar mercado parceiro: ' . $model->nome_mercado;
$this->params['breadcrumbs'][] = ['label' => 'Mercados parceiros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h1 class="h4 mb-3"><?= Html::encode($this->title) ?></h1>
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
