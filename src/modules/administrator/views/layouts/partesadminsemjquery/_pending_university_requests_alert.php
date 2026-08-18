<?php

use yii\helpers\Html;

/** @var int $pendingRequestCount */

if ($pendingRequestCount <= 0) {
    return;
}

$requestLabel = $pendingRequestCount === 1
    ? '1 solicitação pendente'
    : $pendingRequestCount . ' solicitações pendentes';
?>

<?= Html::a(
    '<span><i class="fas fa-exclamation-circle mr-2" aria-hidden="true"></i><strong>'
        . Html::encode($requestLabel)
        . '.</strong> Clique para analisar.</span><i class="fas fa-arrow-right ml-3" aria-hidden="true"></i>',
    ['/administrator/participacao/solicitacoes-correcao-universidade'],
    [
        'class' => 'alert alert-warning shadow-sm d-flex align-items-center justify-content-between mb-4',
        'role' => 'alert',
        'encode' => false,
        'aria-label' => $requestLabel . '. Clique para analisar.',
    ]
) ?>
