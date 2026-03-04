<?php

use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Universidade */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="universidade-form container-fluid">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'enableClientValidation' => true,]); ?>
    <div class="row">
        <div class="col-md-9">
            <?= $form->field($model,  'nome')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'ativo')->widget(Select2::classname(), ['data' => ['1' => 'Ativo', '0' => 'Inativo'], 'options' => ['placeholder' => '- Status -'], 'pluginOptions' => ['allowClear' => true],])->label('Status'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'cidade')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'uf')->dropDownList([
                'AC' => 'AC',
                'AL' => 'AL',
                'AP' => 'AP',
                'AM' => 'AM',
                'BA' => 'BA',
                'CE' => 'CE',
                'DF' => 'DF',
                'ES' => 'ES',
                'GO' => 'GO',
                'MA' => 'MA',
                'MT' => 'MT',
                'MS' => 'MS',
                'MG' => 'MG',
                'PA' => 'PA',
                'PB' => 'PB',
                'PR' => 'PR',
                'PE' => 'PE',
                'PI' => 'PI',
                'RJ' => 'RJ',
                'RN' => 'RN',
                'RS' => 'RS',
                'RO' => 'RO',
                'RR' => 'RR',
                'SC' => 'SC',
                'SP' => 'SP',
                'SE' => 'SE',
                'TO' => 'TO',
            ], ['prompt' => 'Selecione o estado']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'link_doacao_alimento')->textInput(['maxlength' => true]) ?>
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
                    'initialPreview' => $model->icon ? ["/img/{$model->icon}"] : [],
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => [],
                ],
            ])->label('Imagem'); ?>
        </div>
    </div>
</div>

<div class="form-group mt-3">
    <?= Html::submitButton(
        $model->isNewRecord ? 'Criar Universidade' : 'Atualizar Universidade',
        ['class' => 'btn btn-success']
    ) ?>
</div>

<?php ActiveForm::end(); ?>

</div>