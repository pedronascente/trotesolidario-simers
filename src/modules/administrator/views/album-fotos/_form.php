<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model app\modules\common\models\AlbumFoto */
/* @var $participacoes array */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="row">
    <div class="col-md-7">
        <?= $form->field($model, 'participacao_id')->widget(Select2::class, [
            'data' => $participacoes,
            'options' => ['placeholder' => 'Selecione o participante e a edição do trote'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>
    </div>
</div>

<?php if (!$model->isNewRecord && $model->imagem): ?>
    <div class="mb-3">
        <p class="mb-2 font-weight-bold">Imagem atual</p>
        <?= Html::img(['arquivo', 'id' => $model->id], [
            'class' => 'img-thumbnail',
            'style' => 'max-width: 320px; max-height: 220px;',
            'alt' => Html::encode($model->titulo),
        ]) ?>
    </div>
<?php endif; ?>

<?= $form->field($model, 'arquivoImagem')->fileInput(['accept' => 'image/jpeg,image/png,image/gif,image/webp'])
    ->hint($model->isNewRecord ? 'Formatos aceitos: JPG, PNG, GIF e WEBP. Limite de 10 MB.' : 'Deixe vazio para manter a imagem atual.') ?>

<div class="form-group mt-4">
    <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>
<?php ActiveForm::end(); ?>
