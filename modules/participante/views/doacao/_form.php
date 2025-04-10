<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use kartik\widgets\Select2;
use app\modules\participante\models\Trote;
use app\modules\participante\models\Universidade;

/* @var $this yii\web\View */
/* @var $model app\modules\participante\models\Doacao */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-6">
                    <?php
                    //if($teste){
                    //foreach($teste as $value){

                    //  echo 'teste';
                    //}
                    //}
                    ?>
                    <?=
                    $form->field($model, 'file')->label('Anexo doação <br /><span class="badge badge-success">Formatos: JPEG, JPG, GIF e PNG</span>')->widget(FileInput::classname(), [
                        'options' => [
                            'accept' => 'image/*'
                        ],
                        'pluginOptions' => [
                            'resizeImage' => true,
                            //'maxImageWidth' => 640,
                            //'maxImageHeight' => 640,
                            'maxFileSize' => 2500,
                            'resizePreference' => 'width',
                            'showCaption' => false,
                            'showRemove' => false,
                            'showUpload' => false,
                            'browseClass' => 'btn btn-primary btn-block',
                            'browseIcon' => '<i class="fas fa-camera"></i>',
                            'browseLabel' => 'Anexar comprovante de doação',
                            'allowedFileExtensions' => ['jpeg', 'jpg', 'gif', 'png'],
                            'overwriteInitial' => false
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6">
                    <?php if ($model->arquivo) : ?>
                        <img src="/imagens/doacoes/<?= $model->arquivo ?>" class="img-fluid" />
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?=
                    $form->field($model, 'trote_id')->label('Evento')->widget(Select2::classname(), [
                        'options' => ['placeholder' => '- Selecione uma opção -'],
                        'data' => ArrayHelper::map(Trote::find()->where(['ativo' => '1'])->all(), 'id', 'nome')

                    ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?=
                    $form->field($model, 'instituicao')->label('Instituição')->widget(Select2::classname(), [
                        'options' => ['placeholder' => '- Selecione uma opção -'],
                        'data' => ArrayHelper::map(Universidade::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?=
                    $form->field($model, 'tipo_doacao')->label('Tipo de Doação')->widget(Select2::classname(), [
                        'options' => ['placeholder' => '- Selecione uma opção -'],
                        'data' => [
                            'Alimentos' => 'Alimentos',
                            'Sangue' => 'Sangue',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
            </div>


            <div class="form-group">
                <?= Html::submitButton('Salvar', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>