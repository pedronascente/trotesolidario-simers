<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\alert\Alert;
use app\modules\participante\models\Universidade;
use yii\helpers\ArrayHelper;
use app\modules\participante\models\Trote;
?>
<style>
    .field-registerform-outrainstituicao {
        display: none;
    }

    .div-estudante {
        display: none;
    }
</style>
<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-1">
                                <?php
                                if ($error) {
                                    echo Alert::widget([
                                        'type' => Alert::TYPE_DANGER,
                                        'title' => 'Usuário',
                                        'icon' => 'fas fa-ok-circle',
                                        'body' => $msg,
                                        'showSeparator' => true,
                                        'delay' => 5000
                                    ]);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="p-3">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Registre-se</h1>
                                    </div>
                                    <?php $form = ActiveForm::begin(); ?>
                                    <?= $form->field($model, 'name', ['labelOptions' => ['style' => 'color:grey']])->textInput() ?>
                                    <?= $form->field($model, 'password', ['labelOptions' => ['style' => 'color:grey']])->passwordInput() ?>
                                    <?= $form->field($model, 'email', ['labelOptions' => ['style' => 'color:grey']])->textInput(); ?>
                                    <?=
                                    $form
                                        ->field($model, 'trote_id')
                                        ->dropDownList(
                                            ArrayHelper::map(Trote::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
                                            [
                                                'prompt' => '- Selecione o Trote -',
                                                'onchange' => 'outraInstituicao()'
                                            ] // $data should be the same as the items provided to a regular yii2 dropdownlist
                                        );
                                    ?>
                                    <?=
                                    $form
                                        ->field($model, 'estudante')
                                        ->label('Você é um estudante?')
                                        ->dropDownList(
                                            [
                                                '' => '',
                                                'Sim' => 'Sim',
                                                'Não' => 'Não'
                                            ],
                                            [
                                                //                                            'prompt' => 'Você é um estudante?',
                                                'onchange' => 'verificaEstudante()'
                                            ]
                                        );
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
                                        ->dropDownList(
                                            ArrayHelper::map(Universidade::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
                                            [
                                                'prompt' => '- Selecione uma Instituição -',
                                                'onchange' => 'outraInstituicao()'
                                            ] // $data should be the same as the items provided to a regular yii2 dropdownlist
                                        );
                                    ?>

                                    <?= $form->field($model, 'outraInstituicao', ['labelOptions' => ['style' => 'color:grey;']])->textInput(); ?>

                                    <?=
                                    $form->field($model, 'telefone', ['labelOptions' => ['style' => 'color:grey;']])->textInput();
                                    ?>
                                    <?=
                                    $form->field($model, 'previsaoFormatura', ['labelOptions' => ['style' => 'color:grey;']])->textInput();
                                    ?>
                                    <?=
                                    $form
                                        ->field($model, 'conheceONas')
                                        ->label('Você conhece o NAS (Núcleo Acadêmico Simers) e os benefícios do associado?')
                                        ->dropDownList(
                                            [
                                                '' => '',
                                                'Sim' => 'Sim',
                                                'Não' => 'Não',
                                                'Não tenho interesse' => 'Não tenho interesse'
                                            ],
                                            [
                                                'onchange' => 'mostraNas()'
                                            ]
                                        );
                                    ?>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-3">
                                    <?= Html::submitButton('Salvar', ['id' => 'btnsalvar', 'class' => 'btn btn-success', 'name' => 'login-button']) ?>
                                    <br>
                                    <br>
                                    <?= 'Ja possui um usuário? Clique ' . Html::a('aqui', ['/participante']) ?>
                                    <br>
                                    <br>
                                    <?= 'Esqueceu sua senha? Clique ' . Html::a('aqui', ['/revoery-password']) ?>
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="modalNas" tabindex="-1" role="dialog" aria-labelledby="modalNas" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="telefoneModalLabel">NAS</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <p>O Núcleo Acadêmico atua como um braço estudantil do Sindicato Médico do Rio Grande do Sul, com intuito de fortalecer a ligação entre o SIMERS, faculdades de Medicina do Sul do país e os futuros médicos.
                            </p>
                            <p>
                                Conheça os serviços mais procurados pelos associados do Núcleo Acadêmico Simers:
                            </p>
                            <ul>
                                <li>Assessoria jurídica;</li>
                                <li>Seguro automotivo, de vida e previdência privada;</li>
                                <li>Descontos gráficos;</li>
                                <li>Descontos UNIMED (30% a 40%);</li>
                                <li>Desconto 10% na Empresa de uniformes - Fil a Fil;</li>
                                <li>Panvel (até 40% de desconto);</li>
                                <li>Medicina Net: acesso gratuito ao maior portal de educação médica;</li>
                                <li>Cinema GNC: desconto na compra de ingressos;</li>
                                <li>Cursos oferecidos pelo NAS a partir de R$ 10,00 com certificação para horas complementares;</li>
                                <li>Elaboração gratuita de currículo;</li>
                                <li>Desconto especial no curso Extensivo OResidente + APP QUESTÕES;</li>
                                <li>CTSEM - Desconto de 10% em todos os cursos oferecidos pela instituição;</li>
                                <li>Assinatura gratuita do GaúchaZH Light e APP Clube do assinante, com até 50% de desconto em mais de 500 parceiros no RS e SC;</li>
                            </ul>
                            <p>....E muito +!</p>

                            <p>►Investimento de apenas R$70,00/ano.</p>

                            <p>Saiba mais em: <a href="http://nucleoacademico.org.br/servicos" target="_blank">nucleoacademico.org.br/servicos</a></p>
                        </div>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a href="http://nucleoacademico.org.br/associe-se" target="_blank" class="btn btn-success" data-method="POST" onclick="">Associa-se</a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/jquery.inputmask.bundle.js"></script>
<script>
    $(document).ready(function() {
        $("#btnsalvar").attr('disabled', true);
        if ($("#registerform-estudante option:selected").val() == 'Sim') {
            $(".div-estudante").show();
        } else {
            $(".div-estudante").hide();
        }
        if ($("#registerform-instituicao option:selected").val() == 'Outra') {
            $(".field-registerform-outrainstituicao").show();
        } else {
            $(".field-registerform-outrainstituicao").hide();
        }
    });
    $("#registerform-telefone").inputmask({
        "mask": "(99) 99999-9999"
    });
    $("#registerform-previsaoformatura").inputmask({
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
        if ($("#registerform-estudante option:selected").val() == 'Sim') {
            $(".div-estudante").show();
        } else {
            $(".div-estudante").hide();
        }
    }

    function outraInstituicao() {
        if ($("#registerform-instituicao option:selected").text() == 'Outra') {
            $(".field-registerform-outrainstituicao").show();
        } else {
            $(".field-registerform-outrainstituicao").hide();
        }
    }

    function mostraNas() {

        if ($("#registerform-conheceonas option:selected").val() == 'Não') {
            $('#modalNas').modal('toggle');
        }

    }
</script>