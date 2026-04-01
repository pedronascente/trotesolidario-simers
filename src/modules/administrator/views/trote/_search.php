<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\TroteSearchModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trote-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>
    <?= $form->field($model, 'titulo') ?>
    <?= $form->field($model, 'edicao') ?>
    <?= $form->field($model, 'status')->dropDownList(\app\modules\common\models\Trote::getStatusList(), ['prompt' => 'Todos']) ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Limpar', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
