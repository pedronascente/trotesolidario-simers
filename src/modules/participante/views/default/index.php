<?php

use app\modules\common\models\Helper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

?>

<div class="container">
    <!-- Nested Row within Card Body -->
    <div class="row">
        <div class="col-lg-9 mx-auto">
            <div style="padding: 15px 0;">
                <?php if ($capa && ($capa->img_dsk || $capa->img_mob) && $capa->ativo == 1 && $capa->tipo == "Login"): ?>
                    <img
                        src="/img/<?= !Helper::isMobile() ? $capa->img_dsk : $capa->img_mob ?>"
                        style="width:100%;height:auto;">
                <?php else: ?>
                    echo 'Nenhuma capa foi registrada';
                <?php endif; ?>
            </div>
            <div class="px-4">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'class' => 'user'
                ]);
                ?>

                <div class="row">
                    <div class="col-md-7">
                        <?= $form->field($model, 'cpf')->widget(MaskedInput::class, [
                            'mask' => '999.999.999-99',
                            'options' => [
                                'autofocus' => true,
                                'placeholder' => 'Digite seu CPF',
                                'id' => 'cpf',
                                'class' => 'form-control'
                            ]
                        ]) ?>
                    </div>
                    <div class="col-md-5 d-flex justify-content-center" style="flex-direction: column;">
                        <?= Html::submitButton('Entrar', [
                            'id' => 'btnlogin',
                            'class' => 'btn',
                            'style' => 'background: #1f1d44; color: #fdb813;font-weight: 700;',
                            'name' => 'login-button'
                        ]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-7">
                        <?= $form->field($model, 'password')->passwordInput([
                            'id' => 'password',
                            'placeholder' => 'Senha'
                        ]) ?>
                    </div>
                    <div class="col-md-5 d-flex justify-content-center" style="flex-direction: column;">
                        <?= $form->field($model, 'rememberMe', [
                            'options' => ['class' => 'checkbox checkbox-primary'],
                        ])->checkbox(['id' => 'rememberMe']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="row">
                <div class="col-md-8 mx-auto" style="display: flex; flex-direction: column; align-items: center;">
                    <div><?= 'Registre-se para participar ' . Html::a('aqui', ['/participante/register']) ?></div>
                    <div><?= 'Esqueceu sua senha? Clique ' . Html::a('aqui', ['/participante/recovery-password']) ?></div>
                    <div><?= 'Dúvidas clique ' . Html::a('aqui', 'https://nucleoacademico.com.br/#signup', ['target' => '_blank']) ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="background: #00b38d; border-radius: 36px; padding: 10px 30px; margin: 20px 0;">
                    <p style="margin-bottom: 0rem;font-weight: 700; color:#fff;">Escolha a Universidade que <em style="color:#1f1d44;font-style: normal;">você</em> deseja promover neste Trote Solidário e <em style="color:#1f1d44;font-style: normal;">faça a sua doação de alimentos.</em></p>
                </div>
                <div class="col-md-12">
                    <p style="margin-bottom: 0rem;font-weight: 700; color:#1f1d44;">Participe! Sua ação não é trote, é solidariedade de verdade.</p>
                </div>
            </div>
            <div class="row" style="margin: 1rem 0;">
                <?php foreach ($universidades_botoes as $universidade) : ?>
                    <div class="col-md-4">
                        <a class="btn btn-md" style="background: #1f1d44;color:#fff; font-weight: 700;width: 90%;height:90%;margin:5px" href="<?= $universidade->link_doacao_alimento ?>" target="_blank" title="">
                            <?= $universidade->nome ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>