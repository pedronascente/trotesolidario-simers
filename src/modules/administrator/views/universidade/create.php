<?php

use yii\helpers\Html;

/* @var $model app\modules\common\models\Universidade */

$this->title = 'Nova universidade';
$this->params['breadcrumbs'][] = ['label' => 'Universidades', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="card-body"><?= $this->render('_form', ['model' => $model]) ?></div>
</div>