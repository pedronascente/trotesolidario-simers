<?php

use yii\helpers\Html;

$this->title = 'Editar participacao';
$this->params['breadcrumbs'][] = ['label' => 'Participacoes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="p-3"><?= $this->render('_form', compact('model', 'users', 'trotes', 'universidades')) ?></div>
</div>