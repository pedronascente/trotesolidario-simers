<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="doacao-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>
    <?= $form->field($model, 'cpf_snapshot') ?>
    <?= $form->field($model, 'edicao_snapshot') ?>
    <?= $form->field($model, 'status')->dropDownList(\app\modules\common\models\Doacao::getStatusList(), ['prompt' => 'Todos']) ?>

    <div class="form-group">
        <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Limpar', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
