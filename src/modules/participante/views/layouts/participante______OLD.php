<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

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
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/participante/default/">

                <div class="sidebar-brand-text mx-3">Trote Solidário</div>
            </a>

            <hr class="sidebar-divider">

          

            <li class="nav-item <?= $menu_active == 'doacao' ? 'active' : '' ?>">
                <a class="nav-link" href="/participante/doacao">
                    <i class="fa fa-certificate" aria-hidden="true"></i>
                    <span>Doações</span></a>
            </li>
            <li class="nav-item <?= $menu_active == 'certificado' ? 'active' : '' ?>">
                <a class="nav-link" href="/participante/certificado">
                    <i class="fas fa-file-contract"></i>
                    <span>Certificado</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>


        </ul>
        <!-- End of Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <!--                                <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button">
                                                    <i class="fas fa-search fa-sm"></i>
                                                </button>
                                            </div>-->
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small"><?= Yii::$app->user->identity->name ?></span>
                                <i class="fas fa-chevron-down fa-sm fa-fw ml-2 text-gray-400"></i>
                                <!--<img class="img-profile rounded-circle" src="img/undraw_profile.svg">-->
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="/participante/users/perfil">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Sair
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                <?php $this->beginBody() ?>
                <?= $content ?>
                <?php $this->endBody() ?>
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright © Simers 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
    </div>
    <!-- End of Content Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
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
                    <?= Html::beginForm(['/participante/default/logout'], 'post', ['class' => 'd-inline']) ?>
                        <?= Html::submitButton('Sair', ['class' => 'btn btn-primary']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="/layoutadmin/vendor/jquery/jquery.min.js"></script>
    <script src="/layoutadmin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="/layoutadmin/vendor/jquery-easing/jquery.easing.min.js"></script>


    <!-- Custom scripts for all pages-->
    <script src="/layoutadmin/js/sb-admin-2.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownToggle = document.querySelector('#userDropdown');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    var menu = dropdownToggle.nextElementSibling;
                    if (menu && menu.classList.contains('dropdown-menu')) {
                        menu.classList.toggle('show');
                    }
                });
            }
        });
    </script>
</body>

</html>
<?php $this->endPage() ?>