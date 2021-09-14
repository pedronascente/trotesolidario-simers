<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5" style="background-color:#A22164">
                <div class="card-body p-0" >
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 bg-login-image"></div>
                        <div class="col-lg-6 p-5" style="background-color:#fff">
                            <div class="text-center" style="padding: 15px;">
                                <img src="/imagens/LOGO NAS.png" style="width: auto;height: 80px;">
                            </div>
                            <?php
                            $form = ActiveForm::begin([
                                        'id' => 'login-form',
                                        'class' => 'user'
                            ]);
                            ?>
                            <?= $form->field($model, 'username', ['labelOptions' => ['style' => 'color:grey']])->textInput(['id' => 'username', 'autofocus' => true]) ?>
                            <?= $form->field($model, 'password', ['labelOptions' => ['style' => 'color:grey']])->passwordInput(['id' => 'password']) ?>
                            <?=
                                    $form->field($model, 'rememberMe', [
                                        'options' => ['class' => 'checkbox checkbox-primary'],
                                        'labelOptions' => ['style' => 'color:grey']])
                                    ->checkbox([
                                        'id' => 'rememberMe',
                                    ])
                            ?>
                            <?= Html::submitButton('Login', ['id' => 'btnlogin', 'class' => 'btn btn-success', 'name' => 'login-button']) ?>
                        
                            <br><?= 'Registre-se para participar ' . Html::a('aqui', ['/admin/register']) ?>
                            <br><?= 'Esqueceu sua senha? Clique ' . Html::a('aqui', ['/admin/recovery-password']) ?>

<br><?= 'Duvidas clique ' . Html::a('aqui', 'http://nucleoacademico.com.br/contato', ['target' => '_blank']) ?>
<?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>