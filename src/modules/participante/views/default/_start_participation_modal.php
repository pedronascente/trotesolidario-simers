<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="modal fade" id="startParticipationModal" tabindex="-1" role="dialog" aria-labelledby="startParticipationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="startParticipationModalLabel">Criar participacao</h5>
                    <small class="text-muted">
                        Trote ativo: <?= Html::encode($troteAtivoGlobal->titulo ?: ('Trote ' . $troteAtivoGlobalDisplayEdition)) ?>
                    </small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Informe sua universidade e curso para cadastrar sua participacao no trote ativo. Usuario, trote e status serao preenchidos automaticamente pelo sistema.
                </p>

                <?php $form = ActiveForm::begin([
                    'action' => ['home'],
                    'enableClientValidation' => true,
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                        'options' => ['class' => 'form-group'],
                        'inputOptions' => ['class' => 'form-control'],
                        'errorOptions' => ['class' => 'invalid-feedback d-block'],
                    ],
                ]); ?>

                <?= Html::hiddenInput('participation_form', 'start-active-trote') ?>

                <?= $form->errorSummary($startParticipationModel, ['class' => 'alert alert-danger']) ?>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($startParticipationModel, 'universidade_id')->dropDownList(
                            $startParticipationUniversidades,
                            ['prompt' => 'Selecione uma universidade']
                        ) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($startParticipationModel, 'curso')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Cancelar</button>
                    <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
