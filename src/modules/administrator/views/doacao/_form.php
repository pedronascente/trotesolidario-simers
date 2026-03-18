<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\file\FileInput;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\common\models\Evento;
use yii\helpers\Json;

$eventosDoTrote = [];

if ($model->trote_id) {
    $eventosDoTrote = ArrayHelper::map(
        Evento::find()
            ->where(['trote_id' => $model->trote_id])
            ->orderBy('nome')
            ->all(),
        'id',
        'nome'
    );
}

?>

<div class="container-fluid">
    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data'
        ],
        'enableClientValidation' => true,
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'options' => ['class' => 'form-group'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'trote_id')->dropDownList($trote, ['id' => 'trote-id', 'prompt' => 'Selecione']); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'evento_id')->widget(DepDrop::class, [
                'data' => $eventosDoTrote,
                'options' => [
                    'id' => 'evento-id',
                    'value' => $model->evento_id
                ],
                'pluginOptions' => [
                    'depends' => ['trote-id'],
                    'placeholder' => 'Selecione...',
                    'url' => Url::to(['/administrator/doacao/eventos-by-trote']),
                    'initialize' => true
                ]
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'user_id')->dropDownList($usuario, ['prompt' => 'Selecione']); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'tipo_doacao_id')->dropDownList([], ['id' => 'tipo-doacao-id', 'prompt' => 'Selecione']); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'universidade_id')->dropDownList($universidade, ['prompt' => 'Selecione']); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'file')->widget(FileInput::class, [
                'options' => ['accept' => 'image/*'],
                'pluginOptions' => [
                    'showCaption' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'browseClass' => 'btn btn-primary btn-block',
                    'browseIcon' => '<i class="fas fa-camera"></i> ',
                    'browseLabel' => 'Anexar imagem',
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif'],
                    'maxFileSize' => 2048,
                    'overwriteInitial' => true,
                    'initialPreview' => $model->arquivo
                        ? ["/imagens/doacoes/{$model->arquivo}"]
                        : [],
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => $model->arquivo ? [[
                        'caption' => $model->arquivo
                    ]] : [],
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

<?php

$tipoSelecionado = Json::encode($model->tipo_doacao_id);

$this->registerJs("

/* ===============================
   VALIDACAO VISUAL DO FORMULARIO
=================================*/
$('form').on('afterValidate', function () {
    $('.form-group').each(function(){
        if($(this).hasClass('has-error')){
            $(this).find('.form-control').addClass('is-invalid');
        }
    });
});


/* ===============================
   VARIAVEIS
=================================*/
let tipoSelecionado = $tipoSelecionado;


/* ===============================
   CARREGAR TIPOS DE DOAÇÃO
=================================*/
function carregarTiposDoacao(){

    let user  = $('#doacao-user_id').val();
    let trote = $('#trote-id').val();

    if(user && trote){
        $.get('/administrator/doacao/tipos-disponiveis', {
            user_id: user,
            trote_id: trote
        }, function(data){
            let result = (typeof data === 'object') ? data : JSON.parse(data);
            let select = $('#tipo-doacao-id');
            select.empty();
            select.append('<option value=\"\">Selecione</option>');
            $.each(result.output, function(id, nome){
                let selected = (tipoSelecionado == id) ? 'selected' : '';
                select.append(
                    '<option value=\"'+id+'\" '+selected+'>'+nome+'</option>'
                );
            });
        });
    }
}

/* ===============================
   EVENTOS
=================================*/
$('#doacao-user_id').on('change', carregarTiposDoacao);
$('#trote-id').on('change', carregarTiposDoacao);

/* ===============================
   EXECUTA AO ABRIR UPDATE
=================================*/
$(document).ready(function(){
    carregarTiposDoacao();
});

");
?>