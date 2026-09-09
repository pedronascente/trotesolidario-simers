<?php

use yii\helpers\Html;

/* @var $model app\modules\common\models\MercadoUniversidade */
/* @var $mercados array */
/* @var $universidades array */

$this->title = 'Vincular mercado a universidade';
$this->params['breadcrumbs'][] = ['label' => 'Vínculos de mercados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h1 class="h4 mb-3"><?= Html::encode($this->title) ?></h1>
            <?= $this->render('_form', [
                'model' => $model,
                'mercados' => $mercados,
                'universidades' => $universidades,
            ]) ?>
        </div>
    </div>
</div>
