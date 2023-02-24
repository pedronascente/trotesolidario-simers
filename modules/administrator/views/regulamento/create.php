<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\participante\models\Regulamento */

$this->title = 'Create Regulamento';
$this->params['breadcrumbs'][] = ['label' => 'Regulamentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regulamento-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
