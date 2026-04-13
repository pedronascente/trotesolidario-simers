<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Recuperar senha';
?>

<div class="container" style="max-width: 520px; margin-top: 48px;">
    <div class="card shadow-lg border-0">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center mb-4">
                <h2 class="h4 mb-2">Recuperar senha</h2>
                <p class="text-muted mb-0">Informe o e-mail e o CPF para receber o link de redefinicao.</p>
            </div>

            <?php $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'options' => ['class' => 'form-group mb-3'],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
            <?= $form->field($model, 'cpf')->textInput() ?>

            <div class="d-grid gap-2 mt-4">
                <?= Html::submitButton('Enviar link', ['class' => 'btn btn-success btn-block']) ?>
            </div>

            <div class="text-center mt-4">
                <?= Html::a('Voltar ao login', ['/auth/login'], ['class' => 'd-block']) ?>
                <?= Html::a('Criar cadastro', ['/participante/register/index'], ['class' => 'd-block mt-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
<script>
    $(function () {
        $('#participantrequestpasswordresetform-cpf').inputmask({ mask: '999.999.999-99' });
    });
</script>
