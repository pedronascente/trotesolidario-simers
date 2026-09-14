<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\User */

$this->title = 'Novo usuário';
$this->params['breadcrumbs'][] = ['label' => 'Usuários', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="p-3"><?= $this->render('_form', ['model' => $model]) ?></div>
</div> 