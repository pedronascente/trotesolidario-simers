<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="tipo-doacao-form">

    <!-- ALERTA -->
    <?php if ($model->hasErrors('nome')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $model->getFirstError('nome') ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    <?php endif; ?>


    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group', 'novalidate' => true],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'carga_horaria')->textInput() ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'pontuacao_ranking')->textInput() ?>
        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'ativo')->dropDownList([
                1 => 'Sim',
                0 => 'Não',
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'descricao')->textarea(['rows' => 3]) ?>
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
$(document).ready(function () {

    let nomeField = $('#tipodoacao-nome');

    // Se tem erro no nome
    if ($('.field-tipodoacao-nome').hasClass('has-error')) {

        // foco no campo
        nomeField.focus();

        // adiciona estilo bootstrap 4
        nomeField.addClass('is-invalid');
    }

});

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