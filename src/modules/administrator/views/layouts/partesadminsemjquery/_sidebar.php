<?php

use yii\helpers\Url;

$controller = Yii::$app->controller->id;
?>

<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion">

    <a class="sidebar-brand d-flex align-items-center justify-content-center"
        href="<?= Url::to(['/administrator/default']) ?>">
        <div class="sidebar-brand-text mx-3">Trote Solidário</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= $controller === 'default' ? 'active' : '' ?>">
        <a class="nav-link" href="<?= Url::to(['/administrator/default']) ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">Menu</div>

    <?php
    $menus = [
        'trote' => ['label' => 'Trote', 'icon' => 'fa-book'],
        'evento' => ['label' => 'Evento', 'icon' => 'fa-book'],
        'tipo-doacao' => ['label' => 'Tipo Doação', 'icon' => 'fa-donate'],
        'universidade' => ['label' => 'Universidade', 'icon' => 'fa-university'],
        'informativo' => ['label' => 'Informativo', 'icon' => 'fa-info-circle'],
        'regulamento' => ['label' => 'Regulamento', 'icon' => 'fa-bookmark'],
        'banner' => ['label' => 'Banners', 'icon' => 'fa-cubes'],
        'doacao' => ['label' => 'Doações', 'icon' => 'fa-certificate'],
        'users' => ['label' => 'Inscritos', 'icon' => 'fa-users'],
    ];

    foreach ($menus as $id => $menu): ?>
        <li class="nav-item <?= $controller === $id ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Url::to(['/administrator/' . $id]) ?>">
                <i class="fa <?= $menu['icon'] ?>"></i>
                <span><?= $menu['label'] ?></span>
            </a>
        </li>
    <?php endforeach; ?>

</ul>