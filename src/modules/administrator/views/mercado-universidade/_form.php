<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\modules\common\models\MercadoUniversidade */
/** @var $mercados array */
/** @var $universidades array */

?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{hint}\n{error}",
        'options' => ['class' => 'form-group'],
        'inputOptions' => ['class' => 'form-control'],
        'hintOptions' => ['class' => 'form-text text-muted'],
        'errorOptions' => ['class' => 'invalid-feedback d-block'],
    ],
]); ?>

<?= $form->errorSummary($model, [
    'class' => 'alert alert-danger',
    'header' => '<strong>Não foi possível criar o vínculo.</strong>',
]) ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'mercado_id')->dropDownList($mercados, [
            'prompt' => 'Selecione um mercado',
            'autofocus' => true,
        ])->hint('O endereço ajuda a diferenciar mercados com nomes semelhantes.') ?>
    </div>

    <div class="col-md-6">
        <?= $form->field($model, 'universidade_id')->dropDownList($universidades, [
            'prompt' => 'Selecione uma universidade',
        ])->hint('A universidade ficará vinculada ao mercado selecionado.') ?>
    </div>
</div>

<div class="form-group d-flex flex-wrap align-items-center mb-0 mt-2">
    <?= Html::submitButton('<i class="fas fa-link mr-1"></i> Criar vínculo', [
        'class' => 'btn btn-success mr-2 mb-2',
    ]) ?>
    <?= Html::a('<i class="fas fa-arrow-left mr-1"></i> Voltar', ['index'], [
        'class' => 'btn btn-secondary mb-2',
    ]) ?>
</div>

<?php ActiveForm::end(); ?>
