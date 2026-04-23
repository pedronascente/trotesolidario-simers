<?php

use yii\helpers\Html;
use app\assets\AdminAsset;
use app\widgets\Alert;

AdminAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
</head>


<body class="">
    <?php $this->beginBody() ?>
    <div class="login-container d-flex align-items-center justify-content-center">
        <div class="card shadow-lg p-4 login-card">
            <div class="text-center mb-4">
                <h3 class="font-weight-bold">Trote Solidário</h3>
                <p class="text-muted">Acesse sua conta</p>
            </div>

            <div class="login-card-content">
                <?= Alert::widget() ?>
                <?= $content ?> <!-- A VIEW VAI ENTRAR AQUI -->
            </div>

        </div>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage(); ?>
