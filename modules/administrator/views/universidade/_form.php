<?php

use app\modules\participante\models\Trote;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\participante\models\Universidade */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="universidade-form container-fluid">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">


            <?= $form->field($model, 'nome') ?>
            <?= $form->field($model, 'link_doacao_alimento') ?>
            <?= $form->field($model, 'trote_id')->label('Evento')->widget(Select2::classname(), [
                'options' => ['placeholder' => '- Selecione uma opção -'],
                'data' => ArrayHelper::map(Trote::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
            ]);
            ?>
            <?=
            $form->field($model, 'file')->label('Imagem')->widget(FileInput::classname(), [
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
            ]);
            ?>
            <?= $form->field($model, 'ativo')->label('Ativo')->widget(Select2::classname(), [
                'options' => ['placeholder' => '- Status -'],
                'data' => ['1' => 'Ativo', '0' => 'Inativo'],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <img src="/img/<?= $model->icon ?>" class="img-fluid" />
        </div>
    </div>






    <div class="form-group">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>