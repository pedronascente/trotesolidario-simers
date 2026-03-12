<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin([
    'method' => 'get',
]);
?>

<?= $form->field($model, 'id') ?>
<?= $form->field($model, 'status') ?>

<div class="form-group">
    <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>