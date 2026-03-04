<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AdminAsset;
use yii\helpers\Url;
use app\module\common\models\Helper;

AdminAsset::register($this);
$menu_active = Yii::$app->controller->id;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!--Header-->
    <title>Trote Solidário</title>
    <meta name="title" content="Trote Solidário">
    <meta name="description" content="Trote Solidário">
    <meta itemprop="image" content="/imagens/capa.jpg">
    <!--Fim Header-->
    <!--Twiter-->
    <meta property="twitter:description" content="Trote Solidário">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Trote Solidário">
    <meta property="twitter:image" content="/imagens/capa.jpg">
    <!--Fim Twiter-->
    <!--Facebook-->
    <meta property="og:url" content="">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Trote Solidário">
    <meta property="og:description" content="Trote Solidário">
    <meta property="og:image" content="/imagens/favicon-32x32.jpg">
    <!--Fim Facebook-->
    <link rel="shortcut icon" href="/img/favicon_trote.png" type="image/x-icon">
    <!-- Google fonts-->
    <!-- Custom fonts for this template-->
    <link href="/layoutadmin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="/layoutadmin/css/sb-admin-2.css" rel="stylesheet">
    <!-- End Google Analytics -->
    <?php $this->registerCsrfMetaTags() ?>
    <?php $this->head() ?>
</head>
<body class="page-top" id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/administrator/default/">
                <div class="sidebar-brand-text mx-3">Trote Solidário</div>
            </a>
            <hr class="sidebar-divider">
            <li class="nav-item <?= $menu_active == 'trote' ? 'active' : '' ?>">
                <a class="nav-link" href="/administrator/trote">
                    <i class="fa fa-book"></i>
                    <span>Trote</span></a>
            </li>
            <li class="nav-item <?= $menu_active == 'universidade' ? 'active' : '' ?>">
                <a class="nav-link" href="/administrator/universidade">
                    <i class="fa fa-university" aria-hidden="true"></i>
                    <span>Universidade</span></a>
            </li>
            <li class="nav-item <?= $menu_active == 'informativo' ? 'active' : '' ?>">
                <a class="nav-link" href="/administrator/informativo">
                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                    <span>Informativo</span></a>
            </li>
            <li class="nav-item <?= $menu_active == 'regulamento' ? 'active' : '' ?>">
                <a class="nav-link" href="/administrator/regulamento">
                    <i class="fa fa-bookmark" aria-hidden="true"></i>
                    <span>Regulamento</span></a>
            </li>
            <li class="nav-item <?= $menu_active == 'banner' ? 'active' : '' ?>">
                <a class="nav-link" href="/administrator/banner">
                    <i class="fa fa-cubes" aria-hidden="true"></i>
                    <span>Banners</span></a>
            </li>
            <li class="nav-item <?= $menu_active == 'doacao' ? 'active' : '' ?>">
                <a class="nav-link" href="/administrator/doacao">
                    <i class="fa fa-certificate" aria-hidden="true"></i>
                    <span>Doações</span></a>
            </li>
            <li class="nav-item <?= $menu_active == 'users' ? 'active' : '' ?>">
                <a class="nav-link" href="/administrator/users">
                    <i class="fas fa-users"></i>
                    <span>Inscritos</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                        </div>
                    </form>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                    </div>
                                </form>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small"><?= Yii::$app->user->identity->name ?></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Sair
                                </a>
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
                        <span>Copyright © Simers <?php echo date('Y'); ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pronto para sair?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Selecione sair, para finalizar a sessão.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" data-method="POST" href="/administrator/default/logout">Sair</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="/layoutadmin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/layoutadmin/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="/layoutadmin/js/sb-admin-2.js"></script>
</body>

</html>
<?php $this->endPage() ?>