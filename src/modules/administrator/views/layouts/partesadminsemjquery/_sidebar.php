<?php

use yii\helpers\Url;

$controller = Yii::$app->controller->id;

$menuGroups = [
    'Cadastros' => [
        'icon' => 'fa-address-book',
        'items' => [
            'comissoes-organizadoras' => ['label' => 'Comissões organizadoras', 'icon' => 'fa-users'],
            'mercados-parceiros' => ['label' => 'Mercados parceiros', 'icon' => 'fa-store'],
            'universidade' => ['label' => 'Universidades', 'icon' => 'fa-university'],
            'album-fotos' => ['label' => 'Álbum de Fotos', 'icon' => 'fa-images'],
        ],
    ],
    'Comunicacao' => [
        'icon' => 'fa-bullhorn',
        'items' => [
            'banner' => ['label' => 'Banners', 'icon' => 'fa-cubes'],
            'informativo' => ['label' => 'Informativos', 'icon' => 'fa-info-circle'],
            'regulamento' => ['label' => 'Regulamentos', 'icon' => 'fa-bookmark'],
        ],
    ],
    'Operacao' => [
        'icon' => 'fa-cogs',
        'items' => [
            'certificado' => ['label' => 'Certificados', 'icon' => 'fa-file-alt'],
            'doacao' => ['label' => 'Doações', 'icon' => 'fa-certificate'],
            'ranking' => ['label' => 'Ranking', 'icon' => 'fa-trophy'],
        ],
    ],
    'Pessoas' => [
        'icon' => 'fa-users',
        'items' => [
            'participacao' => ['label' => 'Participações', 'icon' => 'fa-user-plus'],
            'user' => ['label' => 'Usuários', 'icon' => 'fa-users'],
        ],
    ],
    'Planejamento' => [
        'icon' => 'fa-folder-open',
        'items' => [
            'evento' => ['label' => 'Eventos', 'icon' => 'fa-calendar'],
            'tipo-doacao' => ['label' => 'Tipos de Doação', 'icon' => 'fa-donate'],
            'trote' => ['label' => 'Trotes', 'icon' => 'fa-book'],
            'mercado-universidade' => ['label' => 'Vincular Mercados a Universidades', 'icon' => 'fa-store'],
           
        ],
    ],
];

$this->registerCss(<<<CSS
.sidebar-hover-menu .nav-item.has-submenu {
    position: relative;
}

.sidebar-hover-menu .nav-item.has-submenu > .nav-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-hover-menu .submenu-toggle {
    width: 100%;
    border: 0;
    background: transparent;
    cursor: pointer;
}

.sidebar-hover-menu .nav-item.has-submenu > .nav-link .menu-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-hover-menu .submenu-arrow {
    transition: transform .2s ease;
    font-size: 12px;
}

.sidebar-hover-menu .submenu {
    list-style: none;
    margin: 0;
    padding: 0;
    background: rgba(255,255,255,.08);
    max-height: 0;
    overflow: hidden;
    transition: max-height .25s ease;
}

.sidebar-hover-menu .nav-item.has-submenu:hover > .submenu,
.sidebar-hover-menu .nav-item.has-submenu:focus-within > .submenu,
.sidebar-hover-menu .nav-item.has-submenu.active-parent > .submenu {
    max-height: 500px;
}

.sidebar-hover-menu .nav-item.has-submenu:hover > .nav-link .submenu-arrow,
.sidebar-hover-menu .nav-item.has-submenu:focus-within > .nav-link .submenu-arrow,
.sidebar-hover-menu .nav-item.has-submenu.active-parent > .nav-link .submenu-arrow {
    transform: rotate(90deg);
}

.sidebar-hover-menu .submenu .nav-item {
    margin: 0;
}

.sidebar-hover-menu .submenu .nav-link {
    display: flex;
    align-items: center;
    padding: .75rem 1rem .75rem 2.7rem;
    color: rgba(255,255,255,.8);
    font-size: 0.92rem;
    text-decoration: none;
    transition: all .2s ease;
}

.sidebar-hover-menu .submenu .nav-link:hover {
    color: #fff;
    background: rgba(255,255,255,.12);
}

.sidebar-hover-menu .submenu .nav-item.active > .nav-link {
    color: #fff;
    font-weight: 700;
    background: rgba(255,255,255,.18);
    border-left: 3px solid #fff;
    padding-left: calc(2.7rem - 3px);
}

.sidebar-hover-menu .nav-item.active-parent > .nav-link {
    color: #fff;
    font-weight: 700;
}

.sidebar-hover-menu .sidebar-heading {
    color: rgba(255,255,255,.55);
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .08em;
}
CSS);

$this->registerJs(<<<JS
document.querySelectorAll('.sidebar-hover-menu .submenu-toggle').forEach(function(button) {
    button.addEventListener('click', function() {
        var parentItem = button.closest('.has-submenu');
        var submenu = document.getElementById(button.getAttribute('aria-controls'));
        var isExpanded = parentItem.classList.toggle('active-parent');
        button.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
        if (submenu) {
            submenu.hidden = !isExpanded;
        }
    });
});
JS);
?>

<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion sidebar-hover-menu">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= Url::to(['/administrator/default']) ?>">
        <span class="administrator-brand-icon"><i class="fas fa-shield-alt" aria-hidden="true"></i></span>
        <span class="administrator-brand-copy">
            <strong>Trote Solidário</strong>
            <small>Administração</small>
        </span>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item <?= $controller === 'default' ? 'active' : '' ?>">
        <a class="nav-link" href="<?= Url::to(['/administrator/default']) ?>" <?= $controller === 'default' ? 'aria-current="page"' : '' ?>>
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <?php $submenuIndex = 0; ?>
    <?php foreach ($menuGroups as $groupLabel => $group): ?>
        <?php
            $isParentActive = false;
            foreach ($group['items'] as $id => $item) {
                if ($controller === $id) {
                    $isParentActive = true;
                    break;
                }
            }
            $submenuId = 'sidebar-submenu-' . $submenuIndex++;
        ?>

        <li class="nav-item has-submenu <?= $isParentActive ? 'active-parent' : '' ?>">
            <button
                type="button"
                class="nav-link submenu-toggle"
                aria-expanded="<?= $isParentActive ? 'true' : 'false' ?>"
                aria-controls="<?= $submenuId ?>"
            >
                <span class="menu-left">
                    <i class="fas <?= $group['icon'] ?>"></i>
                    <span><?= $groupLabel ?></span>
                </span>
                <i class="fas fa-chevron-right submenu-arrow"></i>
            </button>

            <ul class="submenu" id="<?= $submenuId ?>" <?= $isParentActive ? '' : 'hidden' ?>>
                <?php foreach ($group['items'] as $id => $menu): ?>
                    <li class="nav-item <?= $controller === $id ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= Url::to(['/administrator/' . $id]) ?>" <?= $controller === $id ? 'aria-current="page"' : '' ?>>
                            <i class="fa <?= $menu['icon'] ?>"></i>
                            <span class="ml-2"><?= $menu['label'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <hr class="sidebar-divider">
    <?php endforeach; ?>
</ul>
