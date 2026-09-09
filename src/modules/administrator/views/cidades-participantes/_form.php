<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\CidadeParticipante */
?>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-9">
            <?= $form->field($model, 'cidade')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'uf')->textInput(['maxlength' => 2, 'style' => 'text-transform: uppercase;']) ?>
        </div>
    </div>

    <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
<?php ActiveForm::end(); ?>
