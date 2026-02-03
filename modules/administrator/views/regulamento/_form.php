<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Regulamento */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="regulamento-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'file')->label('PDF')->widget(FileInput::classname(), [
        'options' => [
            'accept' => '.pdf'
        ],
        'pluginOptions' => [
            'resizeImage' => true,
            // 'maxImageWidth' => 200,
            // 'maxImageHeight' => 200,
            'resizePreference' => 'width',
            'showCaption' => false,
            'showRemove' => false,
            'showUpload' => false,
            'browseClass' => 'btn btn-primary btn-block',
            'browseIcon' => '<i class="fas fa-camera"></i>',
            'browseLabel' => 'Anexar regulamento',
            'allowedFileExtensions' => ['pdf'],
            'overwriteInitial' => false
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>