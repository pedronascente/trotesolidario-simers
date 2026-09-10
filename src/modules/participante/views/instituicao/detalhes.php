<?php

use app\assets\ParticipantDashboardAsset;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $universidade app\modules\common\models\Universidade */
/* @var $mercadosParceiros app\modules\common\models\MercadoParceiro[] */
/* @var $comissaoOrganizadora app\modules\common\models\ComissaoOrganizadora[] */

ParticipantDashboardAsset::register($this);

$this->title = $universidade->nome;
$logoUrl = $universidade->icon
    ? Url::to('@web/img/' . rawurlencode(basename($universidade->icon)))
    : null;
$hasFoodLink = trim((string) $universidade->link_doacao_alimento) !== '';
$location = trim((string) $universidade->cidade) . ($universidade->uf ? ' - ' . $universidade->uf : '');
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
                    <?php if (!empty($mercadosParceiros)): ?>
                        <p class="institution-details-copy">Confira os mercados parceiros vinculados a esta instituição.</p>
                        <ol class="institution-market-list">
                            <?php foreach ($mercadosParceiros as $mercado): ?>
                                <li>
                                    <i class="fas fa-shopping-basket" aria-hidden="true"></i>
                                    <?= Html::encode($mercado->nome_mercado . ' — ' . $mercado->endereco . ', ' . $mercado->numero . ' - ' . $mercado->bairro) ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <div class="institution-details-empty institution-details-empty-compact">
                            <i class="fas fa-shopping-basket" aria-hidden="true"></i>
                            Nenhum mercado parceiro vinculado a esta instituição no momento.
                        </div>
                    <?php endif; ?>
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
                    <?php if (!empty($comissaoOrganizadora)): ?>
                        <ul class="institution-committee-list">
                            <?php foreach ($comissaoOrganizadora as $membro): ?>
                                <li>
                                    <i class="fas fa-user-circle" aria-hidden="true"></i>
                                    <span>
                                        <?= Html::encode($membro->nome) ?>
                                        <?php if ($membro->cargo): ?><small class="d-block text-muted"><?= Html::encode($membro->cargo) ?></small><?php endif; ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="institution-details-empty institution-details-empty-compact">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            A comissão organizadora desta instituição será divulgada em breve.
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</main>
