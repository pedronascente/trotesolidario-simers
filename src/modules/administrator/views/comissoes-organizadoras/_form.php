<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\ComissaoOrganizadora */
/* @var $trotes array */
/* @var $universidades array */
?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-6">
        <?php if ($model->isNewRecord): ?>
            <?= $form->field($model, 'trote_id')->widget(Select2::class, [
                'data' => $trotes,
                'options' => ['placeholder' => 'Selecione o trote ativo'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        <?php else: ?>
            <?= $form->field($model, 'trote_id')->widget(Select2::class, [
                'data' => $trotes,
                'options' => ['disabled' => true],
            ]) ?>
            <?= Html::activeHiddenInput($model, 'trote_id') ?>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'universidade_id')->widget(Select2::class, [
            'data' => $universidades,
            'options' => ['placeholder' => 'Selecione a instituição'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-8">
        <?= $form->field($model, 'cargo')->textInput(['maxlength' => true])->hint('Opcional. Ex.: Coordenação, Comunicação ou Voluntariado.') ?>
    </div>
    <div class="col-md-4">
        <?= $form->field($model, 'ordem')->input('number', ['min' => 0]) ?>
    </div>
</div>
<?= $form->field($model, 'ativo')->checkbox() ?>
<div class="form-group mt-4">
    <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>
