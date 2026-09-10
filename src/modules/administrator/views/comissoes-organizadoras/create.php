<?php

use yii\helpers\Html;

$this->title = 'Adicionar membro à comissão';
$this->params['breadcrumbs'][] = ['label' => 'Comissões organizadoras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
        <div class="card-body"><?= $this->render('_form', ['model' => $model, 'universidades' => $universidades]) ?></div>
    </div>
</div>
