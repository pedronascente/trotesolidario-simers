<?php

use app\modules\common\models\Banner;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Banner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="banner-form container-fluid">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'posicao')->widget(Select2::class, [
                'data' => Banner::getPosicoes(),
                'options' => [
                    'placeholder' => '- Selecione a posição do banner'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'ativo')->widget(Select2::class, [
                'data' => [
                    1 => 'Sim',
                    0 => 'Não',
                ],
                'pluginOptions' => [
                    'allowClear' => false,
                ],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">

            <?= $form->field($model, 'file_dsk')->label('Imagem DSK')->widget(FileInput::classname(), [
                'options' => [
                    'accept' => 'image/*'
                ],
                'pluginOptions' => [
                    'resizeImage' => true,
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