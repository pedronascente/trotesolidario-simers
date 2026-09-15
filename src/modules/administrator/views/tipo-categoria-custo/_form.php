<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$form = ActiveForm::begin();
echo $form->errorSummary($model, ['class' => 'alert alert-danger']);
echo $form->field($model, 'nome')->textInput(['maxlength' => true]);
echo $form->field($model, 'descricao')->textarea(['rows' => 3, 'maxlength' => 500]);
echo $form->field($model, 'ativo')->dropDownList([1 => 'Sim', 0 => 'Não']);
echo Html::submitButton('Salvar', ['class' => 'btn btn-success mr-2']);
echo Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']);
ActiveForm::end();
