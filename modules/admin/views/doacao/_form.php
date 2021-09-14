<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\file\FileInput;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Doacao */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-6">
                    <?=
                    $form->field($model, 'file')->label('Anexo doação')->widget(FileInput::classname(), [
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
                            'browseLabel' => 'Anexar comprovante de doação',
                            'allowedFileExtensions' => ['jpg', 'gif', 'png'],
                            'overwriteInitial' => false
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-6">
                    <?php if ($model->arquivo): ?>
                        <img src="/imagens/doacoes/<?= $model->arquivo ?>" class="img-fluid"/>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?=
                    $form->field($model, 'trote')->label('Evento')->widget(Select2::classname(), [
                        'options' => ['placeholder' => '- Selecione uma opção -'],
                        'data' => [
                            'Trote 2021/2' => 'Trote 2021/2'
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?=
                    $form->field($model, 'instituicao')->label('Instituição')->widget(Select2::classname(), [
                        'options' => ['placeholder' => '- Selecione uma opção -'],
                        'data' => [
                            'UFRGS - Universidade Federal do Rio Grande do Sul' => 'UFRGS - Universidade Federal do Rio Grande do Sul',
                            'ULBRA - Universidade Luterana do Brasil' => 'ULBRA - Universidade Luterana do Brasil',
                            'UNISINOS - Universidade do Vale do Rio dos Sinos' => 'UNISINOS - Universidade do Vale do Rio dos Sinos',
                            'UCS - Universidade de Caxias do Sul' => 'UCS - Universidade de Caxias do Sul',
                            'UPF - Universidade de Passo Fundo' => 'UPF - Universidade de Passo Fundo',
                            'UFFS - Universidade Federal da Fronteira do Sul' => 'UFFS - Universidade Federal da Fronteira do Sul',
                            'UFPEL - Universidade Federal de Pelotas' => 'UFPEL - Universidade Federal de Pelotas',
                            'UFSM - Universidade Federal de Santa Maria' => 'UFSM - Universidade Federal de Santa Maria',
                            'UFN - Universidade Franciscana' => 'UFN - Universidade Franciscana',
                            'UNIVATES - Fundação Vale do Taquari' => 'UNIVATES - Fundação Vale do Taquari',
                            'UNISC - Universidade de Santa Cruz' => 'UNISC - Universidade de Santa Cruz',
                            'UNIPAMPA - Universidade Federal do Pampa' => 'UNIPAMPA - Universidade Federal do Pampa',
                        ],
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
                            'Sangue' => 'Sangue',
                            'Alimentos' => 'Alimentos',
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
