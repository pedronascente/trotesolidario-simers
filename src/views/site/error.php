<?php

use yii\helpers\Html;
use yii\web\HttpException;

/** @var yii\web\View $this */
/** @var Exception $exception */

$statusCode = $exception instanceof HttpException ? $exception->statusCode : 500;
$isNotFound = $statusCode === 404;
$title = $isNotFound ? 'Página não encontrada' : 'Erro interno';
$description = $isNotFound
    ? 'Ops! A página que você está procurando não existe ou foi movida.'
    : 'Não foi possível concluir a operação. Tente novamente ou entre em contato com o suporte.';

$this->title = $title;
?>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center">
        <div class="mb-3">
            <i class="bi bi-exclamation-triangle display-3 text-warning"></i>
        </div>
        <!-- Código -->
        <h1 class="display-1 fw-bold text-primary"><?= Html::encode($statusCode) ?></h1>

        <!-- Título -->
        <h3 class="mb-3"><?= Html::encode($title) ?></h3>

        <!-- Descrição -->
        <p class="text-muted mb-4">
            <?= Html::encode($description) ?>
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
