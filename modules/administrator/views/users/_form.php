<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\alert\Alert;

/* @var $this yii\web\View */
/* @var $model app\modules\participante\models\Users */
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
                    <?= $form->field($model, 'name', ['labelOptions' => ['style' => 'color:grey']])->label('Nome')->textInput() ?>
                    <?= $form->field($model, 'passwordHash', ['labelOptions' => ['style' => 'color:grey']])->label('Senha')->passwordInput() ?>
                    <?= $form->field($model, 'email', ['labelOptions' => ['style' => 'color:grey']])->textInput(); ?>
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
                        Autorizo que o SINDICATO MÉDICO DO RIO GRANDE DO SUL – SIMERS, em razão da ação “TROTE SOLIDÁRIO 2021/2”, disponha dos meus dados pessoais, de acordo com os artigos 7º e 11 da Lei nº 13.709/2018, e autorizo a utilização de minha imagem e/ou voz.
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
                                'Outra' => 'Outra'
                            ],
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
                    <?= Html::submitButton('Salvar', ['id' => 'btnsalvar', 'class' => 'btn btn-success', 'name' => 'login-button']) ?>
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
        if ($("#users-instituicao option:selected").val() == 'Outra') {
            $(".field-users-outrainstituicao").show();
        } else {
            $(".field-users-outrainstituicao").hide();
        }
    }
</script>