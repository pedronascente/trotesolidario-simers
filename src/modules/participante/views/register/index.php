<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Cadastro de participante';
?>

<div class="register-page">
    <header class="register-brand">
        <?= Html::a(
            '<span class="register-brand-icon" aria-hidden="true"><i class="fas fa-graduation-cap"></i></span><span><small>Bem-vindo ao</small><strong>Trote Solidário</strong></span>',
            ['/'],
            ['class' => 'register-brand-link', 'aria-label' => 'Ir para a página inicial']
        ) ?>
        <?= Html::a('<i class="fas fa-sign-in-alt" aria-hidden="true"></i><span>Já tenho acesso</span>', ['/auth/login'], ['class' => 'register-login-link']) ?>
    </header>

    <div class="register-card">
        <aside class="register-intro">
            <div>
                <span class="register-eyebrow">Faça parte dessa jornada</span>
                <h1>Crie sua conta</h1>
                <p>Cadastre seus dados para participar das ações do Trote Solidário e acompanhar sua contribuição.</p>
            </div>
            <ul class="register-benefits" aria-label="Benefícios do cadastro">
                <li><i class="fas fa-check" aria-hidden="true"></i><span>Acompanhe suas doações</span></li>
                <li><i class="fas fa-check" aria-hidden="true"></i><span>Acesse certificados e registros</span></li>
                <li><i class="fas fa-check" aria-hidden="true"></i><span>Consulte o ranking das universidades</span></li>
            </ul>
        </aside>

        <div class="register-content">
            <div class="register-content-header">
                <span>Cadastro de participante</span>
                <h2>Conte um pouco sobre você</h2>
                <p>Os campos marcados com <span aria-hidden="true">*</span> são obrigatórios.</p>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'participant-register-form',
                'enableClientValidation' => true,
                'options' => ['class' => 'register-form', 'novalidate' => true],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'options' => ['class' => 'form-group mb-3'],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <?= $form->errorSummary($model, [
                'class' => 'alert alert-danger register-error-summary',
                'header' => '<strong>Revise os campos indicados:</strong>',
            ]) ?>

            <section class="register-form-section" aria-labelledby="register-access-title">
                <div class="register-section-title">
                    <span aria-hidden="true"><i class="fas fa-user"></i></span>
                    <div><small>Etapa 1</small><h3 id="register-access-title">Dados de acesso</h3></div>
                </div>
                <div class="register-fields-grid">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'name', 'autocapitalize' => 'words', 'placeholder' => 'Nome que aparecerá no certificado', 'autofocus' => true]) ?>
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'type' => 'email', 'inputmode' => 'email', 'autocomplete' => 'email', 'autocapitalize' => 'none', 'spellcheck' => 'false', 'placeholder' => 'seuemail@exemplo.com']) ?>
                    <?= $form->field($model, 'cpf')->textInput(['maxlength' => 14, 'inputmode' => 'numeric', 'autocomplete' => 'username', 'placeholder' => '000.000.000-00']) ?>
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'autocomplete' => 'new-password', 'placeholder' => 'Mínimo de 6 caracteres'])->hint('Use pelo menos 6 caracteres.', ['class' => 'register-field-hint']) ?>
                </div>
            </section>

            <section class="register-form-section" aria-labelledby="register-profile-title">
                <div class="register-section-title">
                    <span aria-hidden="true"><i class="fas fa-book-open"></i></span>
                    <div><small>Etapa 2</small><h3 id="register-profile-title">Perfil acadêmico</h3></div>
                </div>
                <div class="register-fields-grid">
                    <?= $form->field($model, 'estudante')->dropDownList([
                        '' => 'Selecione',
                        'Sim' => 'Sim',
                        'Nao' => 'Nao',
                    ]) ?>
                </div>

                <div class="student-fields" aria-hidden="true">
                    <div class="register-conditional-note"><i class="fas fa-info-circle" aria-hidden="true"></i> Complete as informações acadêmicas.</div>
                    <div class="register-fields-grid">
                        <?= $form->field($model, 'estudanteMedicina')->dropDownList([
                            '' => 'Selecione',
                            'Sim' => 'Sim',
                            'Nao' => 'Nao',
                        ]) ?>
                        <?= $form->field($model, 'previsaoFormatura')->textInput(['placeholder' => 'AAAA/MM', 'inputmode' => 'numeric', 'maxlength' => 7, 'autocomplete' => 'off']) ?>
                    </div>
                    <div class="other-course-field" aria-hidden="true">
                        <?= $form->field($model, 'estudanteOutros')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </section>

            <section class="register-form-section register-consent-section" aria-labelledby="register-consent-title">
                <div class="register-section-title">
                    <span aria-hidden="true"><i class="fas fa-shield-alt"></i></span>
                    <div><small>Etapa 3</small><h3 id="register-consent-title">Consentimentos</h3></div>
                </div>
                <div class="register-consent-card">
                    <p>Li e aceito as condições de tratamento dos meus dados descritas na <?= Html::a('Política de Privacidade', '#', ['id' => 'privacyPolicyLink']) ?>.</p>
                    <?= $form->field($model, 'politicaPrivacidade')->checkbox(['uncheck' => 0]) ?>
                </div>
                <div class="register-consent-card">
                    <p>Autorizo o SIMERS a utilizar meus dados pessoais, imagem e/ou voz para as ações e divulgações do Trote Solidário, nos termos da Lei 13.709/2018.</p>
                    <?= $form->field($model, 'politicaImagem')->checkbox(['uncheck' => 0]) ?>
                </div>
            </section>

            <div class="register-submit-area">
                <?= Html::submitButton('<span>Criar minha conta</span><i class="fas fa-arrow-right" aria-hidden="true"></i>', ['class' => 'btn btn-success btn-block register-submit']) ?>
                <p>Já possui cadastro? <?= Html::a('Entrar na minha conta', ['/auth/login']) ?></p>
                <?= Html::a('Esqueci minha senha', ['/auth/request-password-reset'], ['class' => 'register-password-link']) ?>
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
            <div class="modal-body register-policy-content scrollable-content" id="privacyPolicyContent" tabindex="0">
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
                studentFields.setAttribute('aria-hidden', isStudent ? 'false' : 'true');
            }

            if (!isStudent && estudanteMedicinaField) {
                toggleOtherCourseField();
            }
        }

        function toggleOtherCourseField() {
            var needsOtherCourse = estudanteMedicinaField && estudanteMedicinaField.value === 'Nao';
            if (otherCourseField) {
                otherCourseField.style.display = needsOtherCourse ? 'block' : 'none';
                otherCourseField.setAttribute('aria-hidden', needsOtherCourse ? 'false' : 'true');
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

        function closePrivacyPolicyFallback() {
            if (!privacyPolicyModal || typeof jQuery !== 'undefined') {
                return;
            }

            privacyPolicyModal.classList.remove('show');
            privacyPolicyModal.style.display = 'none';
            privacyPolicyModal.setAttribute('aria-hidden', 'true');
        }
        
        if (privacyPolicyLink && privacyPolicyModal) {
            privacyPolicyModal.querySelectorAll('[data-dismiss="modal"]').forEach(function (control) {
                control.addEventListener('click', closePrivacyPolicyFallback);
            });

            privacyPolicyModal.addEventListener('click', function (event) {
                if (event.target === privacyPolicyModal) {
                    closePrivacyPolicyFallback();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closePrivacyPolicyFallback();
                }
            });

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
