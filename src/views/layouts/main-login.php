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
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>
</head>


<body class="auth-shell">
    <?php $this->beginBody() ?>
    <main class="login-container">
        <section class="login-card" aria-labelledby="auth-brand-title">
            <header class="login-brand">
                <span class="login-brand-icon" aria-hidden="true"><i class="fas fa-graduation-cap"></i></span>
                <div>
                    <span class="login-brand-kicker">Bem-vindo ao</span>
                    <strong id="auth-brand-title">Trote Solidário</strong>
                </div>
            </header>

            <div class="login-card-content">
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </section>
    </main>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage(); ?>
