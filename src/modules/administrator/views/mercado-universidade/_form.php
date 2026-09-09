<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\modules\common\models\MercadoUniversidade */
/** @var $mercados array */
/** @var $universidades array */

?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">

    <div class="col-md-6">
        <?= $form->field($model, 'mercado_id')->dropDownList($mercados, [
            'prompt' => 'Selecione um mercado',
            'autofocus' => true,
        ]) ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'universidade_id')->dropDownList($universidades, [
            'prompt' => 'Selecione uma universidade',
        ]) ?>
    </div>

</div>

<?= Html::submitButton('Salvar', [
    'class' => 'btn btn-success'
]) ?>

<?= Html::a('Voltar', ['index'], [
    'class' => 'btn btn-secondary'
]) ?>

<?php ActiveForm::end(); ?>
