<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$selectedParticipationId = $selectedParticipationId ?? null;
?>
<div class="participant-university-correction-request-form">
    <?php $form = ActiveForm::begin([
        'action' => ['solicitar-correcao-universidade', 'participacao_id' => $selectedParticipationId],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
    <?= $form->field($model, 'participacaoId')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'new_universidade_id')->dropDownList($universidades, ['prompt' => 'Selecione a universidade correta']) ?>
    <?= $form->field($model, 'motivo')->textarea(['rows' => 5, 'placeholder' => 'Descreva por que a universidade cadastrada nesta participacao esta incorreta e qualquer contexto util para a analise.']) ?>

    <div class="form-group mt-3 mb-0 d-flex gap-2">
        <?= Html::submitButton('Enviar solicitacao', ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Cancelar', ['perfil', 'participacao_id' => $selectedParticipationId], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
