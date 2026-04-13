<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$selectedParticipationId = $selectedParticipationId ?? null;
?>
<div class="participant-university-correction-form">
    <?php $form = ActiveForm::begin([
        'action' => ['corrigir-universidade', 'participacao_id' => $selectedParticipationId],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
    <?= $form->field($model, 'participacaoId')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'universidade_id')->dropDownList($universidades, ['prompt' => 'Selecione a nova universidade']) ?>

    <div class="alert alert-info small">
        Use esta correcao apenas quando a universidade tiver sido informada errada no cadastro desta participacao. Se ja existirem doacoes ou certificado, esse ajuste deixa de ser automatico.
    </div>

    <div class="form-group mt-3 mb-0 d-flex gap-2">
        <?= Html::submitButton('Salvar nova universidade', ['class' => 'btn btn-info']) ?>
        <?= Html::a('Cancelar', ['perfil', 'participacao_id' => $selectedParticipationId], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
