<?php

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $model app\modules\common\models\Banner */
    /* @var $form yii\widgets\ActiveForm */
    ?>

<div class="banner-form container-fluid">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'file_dsk')->label('Imagem DSK')->widget(FileInput::classname(), [
                'options' => [
                    'accept' => 'image/*'
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
                    'browseLabel' => 'Anexar imagem',
                    'allowedFileExtensions' => ['jpg', 'gif', 'png'],
                    'overwriteInitial' => false
                ],
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'file_mob')->label('Imagem Mob')->widget(FileInput::classname(), [
                'options' => [
                    'accept' => 'image/*'
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
                    'browseLabel' => 'Anexar imagem',
                    'allowedFileExtensions' => ['jpg', 'gif', 'png'],
                    'overwriteInitial' => false
                ],
            ]); ?>
        </div>

    </div>




    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>