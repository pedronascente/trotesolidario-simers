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

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'enableClientValidation' => true,
    ]); ?>

    <div class="row">
        <div class="col-md-6">

            <!-- Nome -->
            <?= $form->field($model, 'nome')->textInput(['maxlength' => true, 'class' => 'form-control'])?>

            <!-- Link de Doação -->
            <?= $form->field($model, 'link_doacao_alimento')->textInput(['maxlength' => true]) ?>

            <!-- Evento -->
            <?= $form->field($model, 'trote_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Trote::find()->where(['ativo' => 1])->all(), 'id', 'nome'),
                'options' => ['placeholder' => '- Selecione um Evento -'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Evento'); ?>

            <!-- Upload de imagem -->
            <?= $form->field($model, 'file')->widget(FileInput::classname(), [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'showCaption' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'browseClass' => 'btn btn-primary btn-block',
                    'browseIcon' => '<i class="fas fa-camera"></i> ',
                    'browseLabel' => 'Anexar imagem',
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif'],
                    'overwriteInitial' => false,
                    'initialPreview' => $model->icon ? ["/img/{$model->icon}"] : [],
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => [],
                ],
            ])->label('Imagem'); ?>

            <!-- Status -->
            <?= $form->field($model, 'ativo')->widget(Select2::classname(), [
                'data' => ['1' => 'Ativo', '0' => 'Inativo'],
                'options' => ['placeholder' => '- Status -'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Status'); ?>

        </div>

        <!-- Preview da imagem atual -->
        <?php if ($model->icon): ?>
            <div class="col-md-6">
                <label>Imagem atual</label>
                <img src="/img/<?= $model->icon ?>" class="img-fluid img-thumbnail" />
            </div>
        <?php endif; ?>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>