<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="participant-profile-form">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <div class="mb-4">
        <h3 class="h6 text-success font-weight-bold text-uppercase">Dados pessoais</h3>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true, 'placeholder' => 'Digite seu nome completo']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'email')->input('email', ['maxlength' => true, 'placeholder' => 'voce@exemplo.com']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'placeholder' => "Nome de usu\u{00E1}rio"]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'cpf')->textInput(['readonly' => true]) ?>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h3 class="h6 text-success font-weight-bold text-uppercase">Acesso</h3>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'password', ['enableClientValidation' => false])->passwordInput(['placeholder' => 'Preencha apenas para alterar']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'password_confirmation', ['enableClientValidation' => false])->passwordInput(['placeholder' => 'Repita a nova senha']) ?>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h3 class="h6 text-success font-weight-bold text-uppercase"><?= "Informa\u{00E7}\u{00F5}es acad\u{00EA}micas" ?></h3>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'estudante')->dropDownList([1 => 'Sim', 0 => "N\u{00E3}o"]) ?>
            </div>
            <div class="col-md-4" id="medicine-field">
                <?= $form->field($model, 'estudante_medicina')->dropDownList([1 => 'Sim', 0 => "N\u{00E3}o"]) ?>
            </div>
            <div class="col-md-4" id="graduation-field">
                <?= $form->field($model, 'previsao_formatura')->input('datetime-local') ?>
            </div>
        </div>
    </div>

    <div class="form-group mt-3 mb-0 d-flex gap-2">
        <?= Html::submitButton('Salvar perfil', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancelar', ['/participante/default/home'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<JS
function toggleAcademicFields() {
    const estudante = $('#participantprofileform-estudante').val();
    const isStudent = estudante === '1';

    $('#graduation-field').toggle(isStudent);
    $('#medicine-field').toggle(isStudent);

    if (!isStudent) {
        $('#participantprofileform-previsao_formatura').val('');
        $('#participantprofileform-estudante_medicina').val('0');
    }
}

$(document).on('change', '#participantprofileform-estudante', toggleAcademicFields);
toggleAcademicFields();
JS);
?>
