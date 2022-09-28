<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use kartik\alert\Alert;
use app\modules\admin\models\Trote;
use app\modules\admin\models\Universidade;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .field-users-outrainstituicao {
        display: none;
    }

    .div-estudante {
        display: none;
    }
</style>
<div class="users-form">

    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="p-3">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Perfil</h1>
                    </div>
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($model, 'passwordHash', ['labelOptions' => ['style' => 'color:grey']])->label('Senha')->passwordInput() ?>
                    <?= $form->field($model, 'email', ['labelOptions' => ['style' => 'color:grey']])->textInput(); ?>
                    <?=
                    $form
                        ->field($model, 'trote_id')
                        ->label('Trote')
                        ->widget(Select2::classname(), [
                            'options' => [
                                'placeholder' => '-  Selecione o Trote -',
                            ],
                            'data' => ArrayHelper::map(Trote::find()->where(['ativo' => '1'])->all(), 'id', 'nome')
                        ]);
                    ?>
                    <?=
                    $form
                        ->field($model, 'estudante')
                        ->label('Você é um estudante?')
                        ->widget(Select2::classname(), [
                            'options' => [
                                'placeholder' => '- Você é um estudante? -',
                                'onchange' => 'verificaEstudante()'
                            ],
                            'data' => [
                                'Sim' => 'Sim',
                                'Não' => 'Não'
                            ],
                        ]);
                    ?>
                    <small>
                        O Simers utiliza cookies e tecnologias semelhantes, como explicado em nossa <a style="font-weight: bold;text-decoration: underline;" href="https://simers.org.br/politica-privacidade" target="_blank">Política de Privacidade</a>, para melhorar a experiência de usuário. Ao navegar por nosso conteúdo, o usuário aceita tais condições.
                    </small>
                    <?=
                    $form->field($model, 'politicaPrivacidade', [
                        'options' => ['class' => 'checkbox checkbox-primary'],
                        'labelOptions' => ['style' => 'color:grey']
                    ])
                        ->checkbox([
                            'id' => 'politicaPrivacidade',
                            'onchange' => 'habilitaBotao()'
                        ])
                    ?>
                    <small>
                        Autorizo que o SINDICATO MÉDICO DO RIO GRANDE DO SUL – SIMERS, em razão da ação “TROTE SOLIDÁRIO 2022/1”, disponha dos meus dados pessoais, de acordo com os artigos 7º e 11 da Lei nº 13.709/2018, e autorizo a utilização de minha imagem e/ou voz.
                    </small>
                    <?=
                    $form->field($model, 'politicaImagem', [
                        'options' => ['class' => 'checkbox checkbox-primary'],
                        'labelOptions' => ['style' => 'color:grey']
                    ])
                        ->checkbox([
                            'id' => 'politicaImagem',
                            'onchange' => 'habilitaBotao()'
                        ])
                    ?>



                </div>
            </div>
            <div class="col-lg-6 div-estudante">
                <div class="p-3">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Estudante</h1>
                    </div>
                    <?=
                    $form
                        ->field($model, 'instituicao')
                        ->label('Selecione uma Instituição')
                        ->widget(Select2::classname(), [
                            'options' => [
                                'placeholder' => '- Selecione uma Instituição -',
                                'onchange' => 'outraInstituicao()'
                            ],
                            'data' => ArrayHelper::map(Universidade::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
                        ]);
                    ?>

                    <?= $form->field($model, 'outraInstituicao', ['labelOptions' => ['style' => 'color:grey;']])->textInput(); ?>
                    <?=
                    $form->field($model, 'telefone', ['labelOptions' => ['style' => 'color:grey;']])->textInput();
                    ?>
                    <?=
                    $form->field($model, 'previsaoFormatura', ['labelOptions' => ['style' => 'color:grey;']])->textInput();
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="p-3">
                    <small><b>*Alteração de nome deve ser feita através de solicitação por e-mail: nucleoacademico@simers.org.br</b></small>
                    <br><?= Html::submitButton('Salvar', ['id' => 'btnsalvar', 'class' => 'btn btn-success', 'name' => 'login-button']) ?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
<script>
    $(document).ready(function() {

        if (document.getElementById("politicaPrivacidade").checked == true && document.getElementById("politicaImagem").checked == true) {
            $("#btnsalvar").attr('disabled', false);
        } else {
            $("#btnsalvar").attr('disabled', true);
        }
        if ($("#users-estudante option:selected").val() == 'Sim') {
            $(".div-estudante").show();
        } else {
            $(".div-estudante").hide();
        }
        if ($("#users-instituicao option:selected").val() == 'Outra') {
            $(".field-users-outrainstituicao").show();
        } else {
            $(".field-users-outrainstituicao").hide();
        }
    });
    $("#users-telefone").inputmask({
        "mask": "(99) 99999-9999"
    });
    $("#users-previsaoformatura").inputmask({
        "mask": "9999/99"
    });

    function habilitaBotao() {
        if (document.getElementById("politicaPrivacidade").checked == true && document.getElementById("politicaImagem").checked == true) {
            $("#btnsalvar").attr('disabled', false);
        } else {
            $("#btnsalvar").attr('disabled', true);
        }

    }

    function verificaEstudante() {
        if ($("#users-estudante option:selected").val() == 'Sim') {
            $(".div-estudante").show();
        } else {
            $(".div-estudante").hide();
        }
    }

    function outraInstituicao() {
        if ($("#users-instituicao option:selected").text() == 'Outra') {
            $(".field-users-outrainstituicao").show();
        } else {
            $(".field-users-outrainstituicao").hide();
        }
    }
</script>