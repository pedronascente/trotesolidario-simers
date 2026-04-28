<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Cadastro de participante';
?>

<style>
    .register-card {max-width: 960px;margin: 40px auto;}
    .student-fields {display: none;}
    .other-course-field {display: none;}
    .register-card { max-width: 960px; margin: 40px auto;}
    .student-fields {display: none;}
    .other-course-field {display: none;}
    .scrollable-content {max-height: 500px; overflow-y: auto;padding-right: 10px;border-left: 3px solid #007bff;padding-left: 15px;}
    .scrollable-content::-webkit-scrollbar {width: 8px;}
    .scrollable-content::-webkit-scrollbar-track {background: #f1f1f1;border-radius: 10px;}
    .scrollable-content::-webkit-scrollbar-thumb {background: #007bff;border-radius: 10px;}
    .scrollable-content::-webkit-scrollbar-thumb:hover {background: #0056b3;}
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
                        <?= $form->field($model, 'estudanteMedicina')->dropDownList([
                            '' => 'Selecione',
                            'Sim' => 'Sim',
                            'Nao' => 'Nao',
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'previsaoFormatura')->textInput(['placeholder' => 'AAAA/MM']) ?>
                    </div>
                    <div class="col-md-6 other-course-field">
                        <?= $form->field($model, 'estudanteOutros')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block mb-2">
                    O Simers utiliza cookies e tecnologias semelhantes, como explicado em nossa  
                    <a href="#" id="privacyPolicyLink">Política de Privacidade</a>    
                    , para melhorar a experiência de usuário. 
                   Ao navegar por nosso conteúdo, o usuário aceita tais condições.
                </small>
                <?= $form->field($model, 'politicaPrivacidade')->checkbox(['uncheck' => 0]) ?>
            </div>

            <div class="mb-4">
                <small class="text-muted d-block mb-2">
                    Autorizo que o SINDICATO MÉDICO DO RIO GRANDE DO SUL- SIMERS, em razão do TROTE SOLIDÁRIO, disponha de meus dados pessoais, de acordo com os artigos 7º e 11, da Lei 13.709/2018, bem como autorizo a utilização da minha imagem e/ou voz para a finalidade de divulgação do Trote Solidário em postagens em redes sociais do NAS/SIMERS.
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

<!-- Modal Política de Privacidade -->
<div class="modal fade" id="privacyPolicyModal" tabindex="-1" role="dialog" aria-labelledby="privacyPolicyLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyPolicyLabel">Política de Privacidade</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body scrollable-content" id="privacyPolicyContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Carregando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var estudanteField = document.getElementById('participantregistrationform-estudante');
        var estudanteMedicinaField = document.getElementById('participantregistrationform-estudantemedicina');
        var cpfField = document.getElementById('participantregistrationform-cpf');
        var previsaoField = document.getElementById('participantregistrationform-previsaoformatura');
        var studentFields = document.querySelector('.student-fields');
        var otherCourseField = document.querySelector('.other-course-field');

        function applyMask(value, pattern) {
            var digits = value.replace(/\D/g, '');
            var masked = '';
            var index = 0;

            for (var i = 0; i < pattern.length && index < digits.length; i++) {
                if (pattern[i] === '9') {
                    masked += digits[index++];
                } else {
                    masked += pattern[i];
                }
            }

            return masked;
        }

        function toggleStudentFields() {
            var isStudent = estudanteField && estudanteField.value === 'Sim';
            if (studentFields) {
                studentFields.style.display = isStudent ? 'block' : 'none';
            }

            if (!isStudent && estudanteMedicinaField) {
                toggleOtherCourseField();
            }
        }

        function toggleOtherCourseField() {
            var needsOtherCourse = estudanteMedicinaField && estudanteMedicinaField.value === 'Nao';
            if (otherCourseField) {
                otherCourseField.style.display = needsOtherCourse ? 'block' : 'none';
            }
        }

        if (cpfField) {
            cpfField.addEventListener('input', function () {
                cpfField.value = applyMask(cpfField.value, '999.999.999-99');
            });
            cpfField.value = applyMask(cpfField.value, '999.999.999-99');
        }

        if (previsaoField) {
            previsaoField.addEventListener('input', function () {
                previsaoField.value = applyMask(previsaoField.value, '9999/99');
            });
            previsaoField.value = applyMask(previsaoField.value, '9999/99');
        }

        if (estudanteField) {
            estudanteField.addEventListener('change', toggleStudentFields);
        }

        if (estudanteMedicinaField) {
            estudanteMedicinaField.addEventListener('change', toggleOtherCourseField);
        }

        toggleStudentFields();
        toggleOtherCourseField();

        // Gerenciar modal de Política de Privacidade
        var privacyPolicyLink = document.getElementById('privacyPolicyLink');
        var privacyPolicyModal = document.getElementById('privacyPolicyModal');
        var contentLoaded = false;
        
        if (privacyPolicyLink && privacyPolicyModal) {
            privacyPolicyLink.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Se conteúdo já foi carregado, apenas abre o modal
                if (contentLoaded) {
                    if (typeof jQuery !== 'undefined') {
                        jQuery('#privacyPolicyModal').modal('show');
                    } else {
                        privacyPolicyModal.classList.add('show');
                        privacyPolicyModal.style.display = 'block';
                    }
                    return;
                }
                
                var contentDiv = document.getElementById('privacyPolicyContent');
                
                // Mostrar spinner de carregamento
                contentDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Carregando...</span></div></div>';
                
                // Abrir o modal
                if (typeof jQuery !== 'undefined') {
                    jQuery('#privacyPolicyModal').modal('show');
                } else {
                    privacyPolicyModal.classList.add('show');
                    privacyPolicyModal.style.display = 'block';
                }
                
                // Fazer requisição AJAX para carregar o conteúdo
                fetch('/participante/politica-de-privacidade/index')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao carregar: ' + response.statusText);
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Criar parser do HTML
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        
                        // Procurar especificamente pelo .scrollable-content que contém o texto
                        var scrollableDiv = doc.querySelector('.scrollable-content');
                        
                        if (scrollableDiv) {
                            // Extrair apenas o conteúdo interno (sem a div wrapper)
                            contentDiv.innerHTML = scrollableDiv.innerHTML;
                        } else {
                            // Fallback: procurar por outro seletor
                            var mainContent = doc.querySelector('article') || 
                                            doc.querySelector('[role="main"]') ||
                                            doc.querySelector('.container');
                            if (mainContent) {
                                contentDiv.innerHTML = mainContent.innerHTML;
                            }
                        }
                        
                        contentLoaded = true;
                    })
                    .catch(error => {
                        console.error('Erro ao carregar política de privacidade:', error);
                        contentDiv.innerHTML = '<div class="alert alert-danger"><strong>Erro!</strong> Não foi possível carregar a política de privacidade. Tente novamente mais tarde.</div>';
                    });
            });
        }
    });
</script>
