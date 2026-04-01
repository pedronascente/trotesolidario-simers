<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Exception $exception */

$this->title = 'Erro';
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center">
        <div class="mb-3">
            <i class="bi bi-exclamation-triangle display-3 text-warning"></i>
        </div>
        <!-- Código -->
        <h1 class="display-1 fw-bold text-primary">404</h1>

        <!-- Título -->
        <h3 class="mb-3">Página não encontrada</h3>

        <!-- Descrição -->
        <p class="text-muted mb-4">
            Ops! A página que você está procurando não existe ou foi movida.
        </p>

        <!-- Botões -->
        <div class="d-flex justify-content-center gap-2 mb-4">
            <?= Html::a('Voltar', Yii::$app->request->referrer ?? ['/site/index'], [
                'class' => 'btn btn-outline-secondary px-4'
            ]) ?>

            <?= Html::a('Ir para início', ['/site/index'], [
                'class' => 'btn btn-primary px-4'
            ]) ?>
        </div>

        <!-- Linha -->
        <div class="border-top pt-3 text-muted small">
            Rua Coronel Corte Real, 975 - Petrópolis - Porto Alegre <br>
            (51) 3027 - 3737
        </div>

    </div>
</div>