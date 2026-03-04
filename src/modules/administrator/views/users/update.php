<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\modules\admin\models\Users $model */

$this->title = 'Atualizar Cadastro: ' . $model->id . '-' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Atualizar Cadastro', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="users-update x_painel m">

    <center>
        <h1><?= Html::encode($this->title) ?></h1>
    </center>
    <br>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>