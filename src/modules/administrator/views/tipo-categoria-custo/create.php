<?php
$this->title = 'Nova categoria de custo';
$this->params['breadcrumbs'][] = ['label' => 'Categorias de custos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card shadow mb-4"><div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= yii\helpers\Html::encode($this->title) ?></h6></div><div class="card-body"><?= $this->render('_form', compact('model')) ?></div></div>
