<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

?>
<div class="container-fluid">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'enableClientValidation' => true,]); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'trote_id')->dropDownList(
                    $trote,
                    [
                        'id' => 'trote-id',
                        'prompt' => 'Selecione'
                    ]
                ); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'evento_id')->widget(DepDrop::classname(), [
                    'options' => ['id' => 'evento-id'],
                    'pluginOptions' => [
                        'depends' => ['trote-id'],
                        'placeholder' => 'Selecione...',
                        'url' => Url::to(['/administrator/doacao/eventos-by-trote'])
                    ]
                ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'user_id')->dropDownList($usuario, ['prompt' => 'Selecione']); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'tipo_doacao_id')->dropDownList($tipoDoacao, ['prompt' => 'Selecione']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'universidade_id')->dropDownList($universidade, ['prompt' => 'Selecione']); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
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
                        'initialPreview' => $model->arquivo ? ["/img/{$model->arquivo}"] : [],
                        'initialPreviewAsData' => true,
                        'initialPreviewConfig' => [],
                    ],
                ])->label('Imagem'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'status')->dropDownList(\app\modules\common\models\Doacao::getStatusList()); ?>
            </div>
        </div>
        <div class="form-group mt-3">
            <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Voltar', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>