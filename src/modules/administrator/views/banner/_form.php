<?php

use app\modules\common\models\Banner;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Banner */
/* @var $form yii\widgets\ActiveForm */
/* @var $locaisExibicao array */
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

<div class="alert alert-info border-0" role="note">
    Envie uma versão para desktop e outra para celular sempre que possível. Caso apenas uma seja cadastrada,
    ela será utilizada como alternativa nos dois dispositivos. Formatos: JPG ou PNG, até 5 MB por imagem.
</div>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'tipo')->widget(Select2::class, [
            'data' => $locaisExibicao,
            'options' => [
                'placeholder' => '- Selecione onde o banner será exibido'
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

        <?php if (!$model->isNewRecord && $model->img_dsk): ?>
            <div class="mb-3">
                <strong class="d-block mb-2">Imagem desktop atual</strong>
                <?= Html::img('@web/img/' . rawurlencode(basename($model->img_dsk)), [
                    'class' => 'img-thumbnail',
                    'style' => 'max-width: 100%; max-height: 180px;',
                    'alt' => 'Imagem desktop atual do banner',
                ]) ?>
            </div>
        <?php endif; ?>
        <?= $form->field($model, 'file_dsk')->label('Imagem para desktop')->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/jpeg,image/png'
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
                'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
                'overwriteInitial' => false
            ],
        ])->hint('Use uma imagem horizontal e otimizada para telas maiores.'); ?>
    </div>
    <div class="col-md-6">
        <?php if (!$model->isNewRecord && $model->img_mob): ?>
            <div class="mb-3">
                <strong class="d-block mb-2">Imagem mobile atual</strong>
                <?= Html::img('@web/img/' . rawurlencode(basename($model->img_mob)), [
                    'class' => 'img-thumbnail',
                    'style' => 'max-width: 100%; max-height: 180px;',
                    'alt' => 'Imagem mobile atual do banner',
                ]) ?>
            </div>
        <?php endif; ?>
        <?= $form->field($model, 'file_mob')->label('Imagem para celular')->widget(FileInput::classname(), [
            'options' => [
                'accept' => 'image/jpeg,image/png'
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
                'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
                'overwriteInitial' => false
            ],
        ])->hint('Prefira uma composição legível em telas estreitas.'); ?>
    </div>

</div>

<div class="form-group mt-3">
    <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end(); ?>