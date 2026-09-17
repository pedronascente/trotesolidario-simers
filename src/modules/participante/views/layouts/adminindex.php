<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AdminAsset;
use app\assets\ParticipantNavigationAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AdminAsset::register($this);
ParticipantNavigationAsset::register($this);
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
    <title><?= Html::encode($this->title ?: 'Trote Solidário') ?></title>
    <link rel="shortcut icon" href="<?= Html::encode(Url::to('@web/img/favicon_trote.png')) ?>" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body class="page-top participant-shell" id="page-top">
<?php $this->beginBody() ?>
<div id="wrapper">
    <nav class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar" aria-label="Navegação principal">
        <div class="participant-sidebar-header">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= Html::encode(Url::to(['/participante/default/home'])) ?>">
                <span class="sidebar-brand-icon"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span>
                <span class="sidebar-brand-copy">
                    <strong>Trote</strong>
                    <small>Solidário</small>
                </span>
            </a>
            <button type="button" class="participant-sidebar-close d-md-none" data-participant-menu-close aria-label="Fechar menu">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <hr class="sidebar-divider my-0">

        <div class="nav-item <?= $menu_active === 'default' && $action_id === 'home' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/default/home'])) ?>" <?= $menu_active === 'default' && $action_id === 'home' ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </div>

        <hr class="sidebar-divider">

        <div class="sidebar-heading">Minha jornada</div>

        <div class="nav-item <?= $menu_active === 'album-fotos' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/album-fotos'])) ?>" <?= $menu_active === 'album-fotos' ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-images"></i>
                <span>Álbum de fotos</span>
            </a>
        </div>
        <div class="nav-item <?= $menu_active === 'certificado' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/certificado'])) ?>" <?= $menu_active === 'certificado' ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-file-contract"></i>
                <span>Certificados</span>
            </a>
        </div>
        <div class="nav-item <?= $menu_active === 'doacao' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/doacao'])) ?>" <?= $menu_active === 'doacao' ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-hand-holding-heart"></i>
                <span>Doações</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/default/home', '#' => 'doacao-alimentos'])) ?>">
                <i class="fas fa-apple-alt"></i>
                <span>Doar alimentos</span>
            </a>
        </div>
        <div class="nav-item <?= $menu_active === 'instituicao' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/instituicao'])) ?>" <?= $menu_active === 'instituicao' ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-building"></i>
                <span>Instituição</span>
            </a>
        </div>
        <div class="nav-item <?= $menu_active === 'users' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/users/perfil'])) ?>" <?= $menu_active === 'users' ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-user"></i>
                <span>Meu perfil</span>
            </a>
        </div>
        <div class="nav-item <?= $menu_active === 'default' && $action_id === 'ranking' ? 'active' : '' ?>">
            <a class="nav-link" href="<?= Html::encode(Url::to(['/participante/default/ranking'])) ?>" <?= $menu_active === 'default' && $action_id === 'ranking' ? 'aria-current="page"' : '' ?>>
                <i class="fas fa-trophy"></i>
                <span>Ranking</span>
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>

        <hr class="sidebar-divider d-none d-md-block">
    </nav>

    <button type="button" class="participant-sidebar-backdrop" data-participant-menu-close tabindex="-1" aria-label="Fechar menu"></button>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button type="button" class="participant-menu-toggle btn btn-link d-md-none mr-2" data-participant-menu-toggle aria-controls="accordionSidebar" aria-expanded="false">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                    <span class="sr-only">Abrir menu</span>
                </button>

                <div class="participant-topbar-title d-none d-sm-flex">
                    <span class="participant-topbar-title-icon"><i class="fas fa-bolt" aria-hidden="true"></i></span>
                    <span class="participant-topbar-title-copy">
                        <strong><?= Html::encode($this->title ?: 'Minha jornada') ?></strong>
                    </span>
                </div>

                <ul class="navbar-nav ml-auto">
                    <li class="topbar-divider d-none d-sm-block" aria-hidden="true"></li>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="participant-user-avatar" aria-hidden="true"><i class="fas fa-user"></i></span>
                            <span class="participant-user-copy d-none d-lg-flex">
                                <strong><?= Html::encode($displayName) ?></strong>
                            </span>
                            <i class="fas fa-chevron-down fa-sm fa-fw ml-2 participant-user-chevron"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="<?= Html::encode(Url::to(['/participante/users/perfil'])) ?>">
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
            <div class="container-fluid">                    
                <?= $content ?>
            </div>
        </div>

        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Trote Solidário <span aria-hidden="true">•</span> Universidade que transforma</span>
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
            <div class="modal-body">Selecione sair para finalizar a sessão.</div>
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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
