<?php
use app\assets\ParticipantDashboardAsset;
use yii\helpers\Html;
use yii\helpers\Url;

ParticipantDashboardAsset::register($this);

$this->title = 'Instituições';
$cidades = [];
foreach ($universidades as $universidade) {
    $cidade = trim((string) $universidade->cidade);
    $uf = trim((string) $universidade->uf);

    if ($cidade === '') {
        continue;
    }

    $cidadeLabel = $uf !== '' ? $cidade . ' - ' . $uf : $cidade;
    $cidades[strtolower($cidadeLabel)] = $cidadeLabel;
}

natcasesort($cidades);
?>

<main class="container-fluid participant-dashboard institution-directory">
    <section class="institution-directory-hero mb-4" aria-labelledby="institution-directory-title">
        <div>
            <div class="dashboard-eyebrow dashboard-eyebrow-light">Trote Solidário</div>
            <h1 id="institution-directory-title">Instituições participantes</h1>
            <p>Conheça as cidades participantes e acesse a página completa de cada instituição.</p>
        </div>
        <span class="institution-directory-total"><?= count($universidades) ?> instituição(ões)</span>
    </section>

    <section class="card shadow-sm border-0 mb-4" aria-labelledby="participating-cities-title">
        <div class="card-body">
            <div class="dashboard-section-header mb-3">
                <div>
                    <div class="dashboard-eyebrow">Onde o trote acontece</div>
                    <h2 id="participating-cities-title" class="h4 mb-0 text-gray-900">Cidades participantes</h2>
                </div>
            </div>
            <?php if (!empty($cidades)): ?>
                <ul class="institution-city-list" aria-label="Lista de cidades participantes">
                    <?php foreach ($cidades as $cidade): ?>
                        <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i><?= Html::encode($cidade) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="dashboard-empty-state">
                    Nenhuma cidade participante disponível no momento.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section aria-labelledby="institutions-list-title">
        <div class="dashboard-section-header mb-3">
            <div>
                <div class="dashboard-eyebrow">Instituições</div>
                <h2 id="institutions-list-title" class="h4 mb-0 text-gray-900">Encontre sua instituição</h2>
            </div>
        </div>

        <?php if (!empty($universidades)): ?>
            <div class="row">
                <?php foreach ($universidades as $universidade): ?>
                    <?php
                    $logoUrl = $universidade->icon
                        ? Url::to('@web/img/' . rawurlencode(basename($universidade->icon)))
                        : null;
                    ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <?= Html::a(
                            '<article class="card institution-card h-100 shadow-sm border-0">' .
                                '<div class="card-body d-flex flex-column">' .
                                    '<div class="institution-card-logo" aria-hidden="' . ($logoUrl ? 'false' : 'true') . '">' .
                                        ($logoUrl
                                            ? Html::img($logoUrl, ['alt' => 'Logo de ' . $universidade->nome])
                                            : '<i class="fas fa-university" aria-hidden="true"></i>') .
                                    '</div>' .
                                    '<div class="dashboard-eyebrow mb-2">' . Html::encode($universidade->cidade . ($universidade->uf ? ' - ' . $universidade->uf : '')) . '</div>' .
                                    '<h3 class="institution-card-title">' . Html::encode($universidade->nome) . '</h3>' .
                                    '<span class="institution-card-link mt-auto">Visualizar página completa <i class="fas fa-arrow-right" aria-hidden="true"></i></span>' .
                                '</div>' .
                            '</article>',
                            ['detalhes', 'id' => $universidade->id],
                            [
                                'class' => 'institution-card-hit-area',
                                'aria-label' => 'Visualizar página completa de ' . $universidade->nome,
                            ]
                        ) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="dashboard-empty-state">Nenhuma instituição participante disponível no momento.</div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>
