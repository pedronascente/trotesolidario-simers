<?php

use yii\helpers\Url;

$controller = Yii::$app->controller->id;

$menuGroups = [
    'Planejamento' => [
        'trote' => ['label' => 'Trotes', 'icon' => 'fa-book'],
        'evento' => ['label' => 'Eventos', 'icon' => 'fa-calendar'],
        'universidade' => ['label' => 'Universidades', 'icon' => 'fa-university'],
        'tipo-doacao' => ['label' => 'Tipos de Doacao', 'icon' => 'fa-donate'],
    ],
    'Pessoas' => [
        'user' => ['label' => 'Usuarios', 'icon' => 'fa-users'],
        'participacao' => ['label' => 'Participacoes', 'icon' => 'fa-user-plus'],
    ],
    'Operacao' => [
        'doacao' => ['label' => 'Doacoes', 'icon' => 'fa-certificate'],
        'certificado' => ['label' => 'Certificados', 'icon' => 'fa-file-alt'],
        'ranking' => ['label' => 'Ranking', 'icon' => 'fa-trophy'],
    ],
    'Comunicacao' => [
        'banner' => ['label' => 'Banners', 'icon' => 'fa-cubes'],
        'informativo' => ['label' => 'Informativos', 'icon' => 'fa-info-circle'],
        'regulamento' => ['label' => 'Regulamentos', 'icon' => 'fa-bookmark'],
    ],
];
?>

<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= Url::to(['/administrator/default']) ?>">
        <div class="sidebar-brand-text mx-3">Trote Solidario</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= $controller === 'default' ? 'active' : '' ?>">
        <a class="nav-link" href="<?= Url::to(['/administrator/default']) ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <?php foreach ($menuGroups as $groupLabel => $menus): ?>
        <div class="sidebar-heading"><?= $groupLabel ?></div>

        <?php foreach ($menus as $id => $menu): ?>
            <li class="nav-item <?= $controller === $id ? 'active' : '' ?>">
                <a class="nav-link" href="<?= Url::to(['/administrator/' . $id]) ?>">
                    <i class="fa <?= $menu['icon'] ?>"></i>
                    <span><?= $menu['label'] ?></span>
                </a>
            </li>
        <?php endforeach; ?>

        <hr class="sidebar-divider">
    <?php endforeach; ?>
</ul>
