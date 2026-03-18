<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
?>

<div class="regulamento-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'tipo')
        ->hiddenInput([
            'value' => \app\modules\common\models\Documento::TIPO_REGULAMENTO
        ])
        ->label(false) ?>

    <?= $form->field($model, 'file')
        ->label('PDF')
        ->widget(FileInput::classname(), [
            'options' => [
                'accept' => '.pdf'
            ],
            'pluginOptions' => [
                'showCaption' => false,
                'showRemove' => false,
                'showUpload' => false,
                'browseClass' => 'btn btn-primary btn-block',
                'browseLabel' => 'Anexar informativo',
                'allowedFileExtensions' => ['pdf'],
                'overwriteInitial' => false
            ],
        ]); ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php

$this->registerJs("

    /* ===============================
    VALIDACAO VISUAL DO FORMULARIO
    =================================*/

    $('form').on('afterValidate', function () {
        $('.form-group').each(function()
        {
            if($(this).hasClass('has-error'))
            {
                $(this).find('.form-control').addClass('is-invalid');
            }
        });
    });
");
?>