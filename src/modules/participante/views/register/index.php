<?php

use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Cadastro de participante';
$trotes = ArrayHelper::map(Trote::getAtivos(), 'id', fn(Trote $trote) => trim(($trote->titulo ?: 'Trote') . ' | ' . ($trote->edicao ?: '-')));
$universidades = ArrayHelper::map(
    Universidade::find()->where(['ativo' => 1])->orderBy(['nome' => SORT_ASC])->all(),
    'id',
    'nome'
);
?>

<style>
    .register-card {
        max-width: 960px;
        margin: 40px auto;
    }

    .student-fields {
        display: none;
    }

    .other-course-field {
        display: none;
    }
</style>

<div class="container register-card">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <h2 class="h4 mb-2">Cadastro de participante</h2>
                <p class="text-muted mb-0">Preencha seus dados para criar o acesso ao modulo do participante.</p>
            </div>

            <?php $form = ActiveForm::begin([
                'enableClientValidation' => true,
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'options' => ['class' => 'form-group mb-3'],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'cpf')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'estudante')->dropDownList([
                        '' => 'Selecione',
                        'Sim' => 'Sim',
                        'Nao' => 'Nao',
                    ]) ?>
                </div>
            </div>

            <div class="student-fields border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'trote_id')->dropDownList($trotes, ['prompt' => 'Selecione']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'instituicao')->dropDownList($universidades, ['prompt' => 'Selecione']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'estudanteMedicina')->dropDownList([
                            '' => 'Selecione',
                            'Sim' => 'Sim',
                            'Nao' => 'Nao',
                        ]) ?>
                    </div>
                    <div class="col-md-6 other-course-field">
                        <?= $form->field($model, 'estudanteOutros')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'previsaoFormatura')->textInput(['placeholder' => 'AAAA/MM']) ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block mb-2">
                    O Simers utiliza cookies e tecnologias semelhantes, como explicado em nossa politica de privacidade.
                </small>
                <?= $form->field($model, 'politicaPrivacidade')->checkbox(['uncheck' => 0]) ?>
            </div>

            <div class="mb-4">
                <small class="text-muted d-block mb-2">
                    Autorizo o uso da minha imagem, video e voz para divulgacao do Trote Solidario.
                </small>
                <?= $form->field($model, 'politicaImagem')->checkbox(['uncheck' => 0]) ?>
            </div>

            <div class="d-grid gap-2">
                <?= Html::submitButton('Cadastrar', ['class' => 'btn btn-success btn-block']) ?>
            </div>

            <div class="text-center mt-4">
                <?= Html::a('Ja possuo acesso', ['/auth/login'], ['class' => 'd-block']) ?>
                <?= Html::a('Esqueci minha senha', ['/participante/recovery-password/index'], ['class' => 'd-block mt-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
<script>
    function toggleStudentFields() {
        var isStudent = $('#participantregistrationform-estudante').val() === 'Sim';
        $('.student-fields').toggle(isStudent);
    }

    function toggleOtherCourseField() {
        var needsOtherCourse = $('#participantregistrationform-estudantemedicina').val() === 'Nao';
        $('.other-course-field').toggle(needsOtherCourse);
    }

    $(function () {
        $('#participantregistrationform-cpf').inputmask({ mask: '999.999.999-99' });
        $('#participantregistrationform-previsaoformatura').inputmask({ mask: '9999/99' });

        toggleStudentFields();
        toggleOtherCourseField();

        $('#participantregistrationform-estudante').on('change', toggleStudentFields);
        $('#participantregistrationform-estudantemedicina').on('change', toggleOtherCourseField);
    });
</script>
