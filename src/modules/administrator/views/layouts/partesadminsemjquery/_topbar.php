<?php

use yii\helpers\Url;
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow">

    <button class="btn btn-link d-md-none rounded-circle mr-3" id="sidebarToggleTop">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <?php if (!Yii::$app->user->isGuest): ?>
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    <span class="mr-2 text-gray-600 small">
                        <?= Yii::$app->user->identity->name ?>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-right shadow">
                    <a class="dropdown-item" data-method="post"
                        href="<?= Url::to(['/administrator/default/logout']) ?>">
                        <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i>
                        Sair
                    </a>
                </div>
            </li>
        <?php endif; ?>
    </ul>
</nav>