<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
?>

<div class="regulamento-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'tipo')
        ->hiddenInput([
            'value' => \app\modules\common\models\Documento::TIPO_INFORMATIVO
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

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>