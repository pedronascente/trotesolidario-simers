<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="user-form container-fluid px-0">
    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

    <div class="card mb-3">
        <div class="card-header py-3">
            Dados do usuario
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->input('email', ['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'cpf')->textInput([
                        'maxlength' => true,
                        'placeholder' => '000.000.000-00',
                        'class' => 'form-control cpf-mask',
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => $model->scenario === 'create' ? 'Digite a senha' : 'Preencha apenas para alterar',
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'role')->dropDownList(User::getRoleList(), ['prompt' => 'Selecione']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'status')->dropDownList(User::getStatusList(), ['prompt' => 'Selecione']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header py-3">
            Perfil do participante
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    <?= $form->field($model, 'create_participante')->checkbox(['label' => 'Gerenciar este usuario como participante']) ?>
                </div>
            </div>

            <div id="participant-fields">
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'estudante')->dropDownList([
                            1 => 'Sim',
                            0 => 'Nao',
                        ], ['prompt' => 'Selecione']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'estudante_medicina')->dropDownList([
                            1 => 'Sim',
                            0 => 'Nao',
                        ], ['prompt' => 'Selecione']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'previsao_formatura')->input('datetime-local') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton($model->scenario === 'create' ? 'Cadastrar' : 'Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(<<<JS
function applyCpfMask(value) {
    value = value.replace(/\D/g, '').slice(0, 11);
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d)/, '$1.$2');
    value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return value;
}

function toggleParticipantFields() {
    const checked = $('#usercreateform-create_participante').is(':checked');
    $('#participant-fields').toggle(checked);
}

function syncParticipantCheckboxWithRole() {
    const role = $('#usercreateform-role').val();
    const isAdmin = role === 'admin';
    const checkbox = $('#usercreateform-create_participante');

    if (isAdmin) {
        checkbox.prop('checked', false);
        checkbox.prop('disabled', true);
    } else {
        checkbox.prop('disabled', false);
    }

    toggleParticipantFields();
}

$(document).on('input', '.cpf-mask', function () {
    $(this).val(applyCpfMask($(this).val()));
});

$(document).on('change', '#usercreateform-create_participante', toggleParticipantFields);
$(document).on('change', '#usercreateform-role', syncParticipantCheckboxWithRole);

syncParticipantCheckboxWithRole();
JS);
?>
