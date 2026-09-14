<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model \app\modules\common\models\ParticipantProfileForm */
/* @var $selectedParticipationId int|null */

?>

<div class="participant-profile-form">
    <?php $form = ActiveForm::begin([
        'id' => 'participant-profile-form',
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

    <div class="form-group mt-3 mb-0 d-flex flex-wrap">
        <?= Html::submitButton('Salvar perfil', [
            'class' => 'btn btn-success mr-2 mb-2',
            'id' => 'save-profile-button',
        ]) ?>
        <?= Html::a('Cancelar', ['perfil', 'participacao_id' => $selectedParticipationId], [
            'class' => 'btn btn-outline-secondary mb-2',
            'id' => 'cancel-profile-button',
        ]) ?>
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

const profileForm = $('#participant-profile-form');
const initialProfileData = profileForm.serialize();

$('#cancel-profile-button').on('click', function (event) {
    if (profileForm.serialize() !== initialProfileData && !window.confirm('Descartar as altera\u00e7\u00f5es n\u00e3o salvas?')) {
        event.preventDefault();
    }
});

profileForm.on('beforeSubmit', function () {
    $('#save-profile-button')
        .prop('disabled', true)
        .text('Salvando...');

    return true;
});
JS);
?>
