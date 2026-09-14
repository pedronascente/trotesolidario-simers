<?php

use app\modules\common\models\Banner;
use yii\helpers\Html;

$localExibicao = Banner::getLocaisExibicao()[$model->tipo] ?? $model->tipo;
$this->title = 'Editar banner: ' . $localExibicao;
$this->params['breadcrumbs'][] = ['label' => 'Banners', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
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