<?php

use yii\helpers\Url;

$identity = Yii::$app->user->identity;
$displayName = $identity ? $identity->name : 'Administrador';
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">

    <button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop">
        <i class="fa fa-bars"></i>
    </button>

    <div class="administrator-topbar-title d-none d-sm-flex">
        <span class="administrator-topbar-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
        <strong><?= yii\helpers\Html::encode($this->title ?: 'Administração') ?></strong>
    </div>

    <ul class="navbar-nav ml-auto">
        <?php if (!Yii::$app->user->isGuest): ?>
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    <span class="administrator-user-avatar" aria-hidden="true"><i class="fas fa-user-shield"></i></span>
                    <span class="administrator-user-name d-none d-lg-inline"><?= yii\helpers\Html::encode($displayName) ?></span>
                    <i class="fas fa-chevron-down fa-xs ml-2 administrator-user-chevron" aria-hidden="true"></i>
                </a>

                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" data-method="post"
                        href="<?= Url::to(['/auth/logout']) ?>">
                        <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i>
                        Sair
                    </a>
                </div>
            </li>
        <?php endif; ?>
    </ul>
</nav>
