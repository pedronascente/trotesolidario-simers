<?php

use app\assets\ParticipantDashboardAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $universidade app\modules\common\models\Universidade */

ParticipantDashboardAsset::register($this);

$this->title = $universidade->nome;
$logoUrl = $universidade->icon
    ? Url::to('@web/img/' . rawurlencode(basename($universidade->icon)))
    : null;
$hasFoodLink = trim((string) $universidade->link_doacao_alimento) !== '';
$location = trim((string) $universidade->cidade) . ($universidade->uf ? ' - ' . $universidade->uf : '');
$mercadosParceiros = [
    'Mercado Exemplo Central — Av. Principal, 120',
    'Supermercado Exemplo — Rua das Flores, 450',
    'Mercado Solidário Exemplo — Av. Brasil, 980',
    'Rede Parceira Exemplo — Rua do Comércio, 235',
    'Empório Exemplo — Av. da Cidade, 610',
];
$comissaoOrganizadora = [
    'Ana Exemplo',
    'Bruno Demonstração',
    'Carla Ilustrativa',
    'Diego Modelo',
    'Elisa Referência',
];
?>

<main class="container-fluid participant-dashboard institution-details">
    <div class="institution-details-back mb-3">
        <?= Html::a('<i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar para instituições', ['index']) ?>
    </div>

    <section class="institution-details-header mb-4" aria-labelledby="institution-details-title">
        <div class="institution-details-logo">
            <?php if ($logoUrl): ?>
                <?= Html::img($logoUrl, ['alt' => 'Logo de ' . $universidade->nome]) ?>
            <?php else: ?>
                <i class="fas fa-university" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
        <div>
            <div class="dashboard-eyebrow text-success">Instituição participante</div>
            <h1 id="institution-details-title"><?= Html::encode($universidade->nome) ?></h1>
            <?php if ($location !== ''): ?>
                <p><i class="fas fa-map-marker-alt" aria-hidden="true"></i><?= Html::encode($location) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <section class="card shadow-sm border-0 h-100" aria-labelledby="markets-title">
                <div class="card-body">
                    <div class="dashboard-eyebrow">Doação de alimentos</div>
                    <h2 id="markets-title" class="h4 text-gray-900">Mercados parceiros</h2>
                    <p class="institution-details-copy">Os mercados parceiros desta instituição serão divulgados neste espaço.</p>
                    <div class="institution-example-notice">Lista demonstrativa</div>
                    <ol class="institution-market-list">
                        <?php foreach ($mercadosParceiros as $mercado): ?>
                            <li><i class="fas fa-shopping-basket" aria-hidden="true"></i><?= Html::encode($mercado) ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </section>
        </div>

        <div class="col-lg-5 mb-4">
            <section class="card shadow-sm border-0 mb-4" aria-labelledby="food-link-title">
                <div class="card-body">
                    <div class="dashboard-eyebrow">Contribua</div>
                    <h2 id="food-link-title" class="h4 text-gray-900">Doação de alimentos</h2>
                    <p class="institution-details-copy">Acesse a campanha da instituição para realizar sua doação.</p>
                    <?php if ($hasFoodLink): ?>
                        <?= Html::a(
                            '<i class="fas fa-external-link-alt" aria-hidden="true"></i> Acessar campanha de doação',
                            $universidade->link_doacao_alimento,
                            [
                                'class' => 'btn institution-details-action',
                                'target' => '_blank',
                                'rel' => 'noopener noreferrer',
                                'aria-label' => 'Acessar campanha de doação de alimentos de ' . $universidade->nome,
                            ]
                        ) ?>
                    <?php else: ?>
                        <span class="institution-card-unavailable">Link de doação em breve</span>
                    <?php endif; ?>
                </div>
            </section>

            <section class="card shadow-sm border-0" aria-labelledby="committee-title">
                <div class="card-body">
                    <div class="dashboard-eyebrow">Pessoas que fazem acontecer</div>
                    <h2 id="committee-title" class="h4 text-gray-900">Comissão organizadora</h2>
                    <div class="institution-example-notice">Lista demonstrativa</div>
                    <ul class="institution-committee-list">
                        <?php foreach ($comissaoOrganizadora as $membro): ?>
                            <li><i class="fas fa-user-circle" aria-hidden="true"></i><?= Html::encode($membro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</main>
