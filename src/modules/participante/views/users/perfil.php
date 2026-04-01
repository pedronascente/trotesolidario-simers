<?php

use kartik\alert\Alert;
use yii\helpers\Html;

$this->title = 'Meu perfil';
?>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Perfil', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Perfil', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800"><?= Html::encode($this->title) ?></h1>
            <p class="mb-0 text-muted">Atualize seus dados pessoais, senha de acesso e informacoes academicas.</p>
        </div>
        <?= Html::a('Voltar para home', ['/participante/default/home'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100 border-left-success">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-2">Resumo da conta</div>
                    <div class="mb-3">
                        <div class="small text-muted">Nome</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($user->nome) ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">E-mail</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($user->email) ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">CPF</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($user->getCpfFormatado() ?? '-') ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Faculdade</div>
                        <div class="font-weight-bold text-dark"><?= Html::encode($universidadeAtual ?? '-') ?></div>
                    </div>
                    <div class="mb-0">
                        <div class="small text-muted">Perfil academico</div>
                        <div class="font-weight-bold text-dark">
                            <?= (int) $participante->estudante === 1 ? 'Estudante' : 'Nao estudante' ?>
                            <?= (int) $participante->estudante_medicina === 1 ? ' • Medicina' : '' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h2 class="h5 mb-0 text-gray-800">Editar perfil</h2>
                </div>
                <div class="p-3">
                    <?= $this->render('_form', ['model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>
</div>