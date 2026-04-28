<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AdminAsset;
use yii\helpers\Html;  

AdminAsset::register($this);
$menu_active = Yii::$app->controller->id;
$action_id = Yii::$app->controller->action ? Yii::$app->controller->action->id : null;
$identity = Yii::$app->user->identity;
$displayName = $identity ? $identity->name : 'Participante';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Trote Solidario</title>
    <link rel="shortcut icon" href="/img/favicon_trote.png" type="image/x-icon">
    <link href="/layoutadmin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="/layoutadmin/css/sb-admin-2.css" rel="stylesheet">
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body class="page-top" id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/participante/default/home">
            <div class="sidebar-brand-text mx-3">Trote Solidario</div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item <?= $menu_active === 'default' && $action_id === 'home' ? 'active' : '' ?>">
            <a class="nav-link" href="/participante/default/home">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </li>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">Minha jornada</div>

        <li class="nav-item <?= $menu_active === 'doacao' ? 'active' : '' ?>">
            <a class="nav-link" href="/participante/doacao">
                <i class="fas fa-hand-holding-heart"></i>
                <span>Doações</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/participante/default/home#doacao-alimentos">
                <i class="fas fa-apple-alt"></i>
                <span>Doar alimentos</span>
            </a>
        </li>
        <li class="nav-item <?= $menu_active === 'default' && $action_id === 'ranking' ? 'active' : '' ?>">
            <a class="nav-link" href="/participante/default/ranking">
                <i class="fas fa-trophy"></i>
                <span>Ranking</span>
            </a>
        </li>
        <li class="nav-item <?= $menu_active === 'certificado' ? 'active' : '' ?>">
            <a class="nav-link" href="/participante/certificado">
                <i class="fas fa-file-contract"></i>
                <span>Certificados</span>
            </a>
        </li>
        <li class="nav-item <?= $menu_active === 'users' ? 'active' : '' ?>">
            <a class="nav-link" href="/participante/users/perfil">
                <i class="fas fa-user"></i>
                <span>Meu perfil</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= Html::encode($displayName) ?></span>
                            <i class="fas fa-chevron-down fa-sm fa-fw ml-2 text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="/participante/users/perfil">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Meu perfil
                            </a>
                            <?php if ($identity !== null): ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Sair
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </nav>

            <?php $this->beginBody() ?>
            <?= $content ?>
            <?php $this->endBody() ?>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright Trote Solidario</span>
                </div>
            </div>
        </footer>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<?php if ($identity !== null): ?>
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pronto para sair?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            </div>
            <div class="modal-body">Selecione sair para finalizar a sessao.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                <?= Html::beginForm(['/participante/default/logout'], 'post', ['class' => 'd-inline']) ?>
                    <?= Html::submitButton('Sair', ['class' => 'btn btn-primary']) ?>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


</body>
</html>
<?php $this->endPage() ?> 
