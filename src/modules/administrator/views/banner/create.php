<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Banner */
/* @var $locaisExibicao array */

$this->title = 'Cadastrar banner';
$this->params['breadcrumbs'][] = ['label' => 'Banners', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6>
        </div>
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
                'locaisExibicao' => $locaisExibicao,
            ]) ?>
        </div>
    </div>
</div>
