<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\alert\Alert;
?>
<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-lg-6">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6">
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
                            <div class="col-lg-12">
                                <div class="p-3">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Recuperar senha</h1>
                                    </div>
                                    <?php $form = ActiveForm::begin(); ?>
                                    <?= $form->field($model, 'email', ['labelOptions' => ['style' => 'color:grey']])->textInput(); ?>


                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-3">
                                    <?= Html::submitButton('Enviar', ['id' => 'btnsalvar', 'class' => 'btn btn-success', 'name' => 'login-button']) ?>
                                    <br>
                                    <br>
                                    <?= 'Ja possui um usuario clique ' . Html::a('aqui', ['/participante']) ?>
                                    <br>
                                    <?= 'Não possui clique ' . Html::a('aqui', ['/register']) ?>
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