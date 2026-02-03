<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Informativo */

$this->title = 'Create Informativo';
$this->params['breadcrumbs'][] = ['label' => 'Informativos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informativo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
