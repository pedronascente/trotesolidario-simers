<?php
   
use yii\helpers\Html; 
use yii\widgets\ActiveForm;
use app\modules\common\models\Trote;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Trote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trote-form container-fluid">
    <?php $form = ActiveForm::begin([       
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'titulo')
                ->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'numero_edicao')
                ->textInput(['type' => 'number', 'min' => 1]) ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model, 'ano')
                ->textInput(['type' => 'number', 'min' => 2000]) ?>
        </div>
    </div>

    <?= $form->field($model, 'descricao')
        ->textarea(['rows' => 4]) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'status')
                ->dropDownList(
                    Trote::getStatusList(),
                    ['prompt' => 'Selecione o status']
                ) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'data_inicio')
                ->input('date', [
                    'value' => $model->data_inicio ? date('Y-m-d', strtotime($model->data_inicio)) : ''
                ]) ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'data_fim')
                ->input('date', [
                    'value' => $model->data_fim ? date('Y-m-d', strtotime($model->data_fim)) : ''
                ]) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs("

/* ===============================
VALIDACAO VISUAL DO FORMULARIO
=================================*/

$('form').on('afterValidate', function () {
    $('.form-group').each(function()
    {
        if($(this).hasClass('has-error'))
        {
            $(this).find('.form-control').addClass('is-invalid');
        }
    });
});

");
?>