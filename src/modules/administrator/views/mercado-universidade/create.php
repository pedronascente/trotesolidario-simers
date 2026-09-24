<?php

use yii\helpers\Html;

/* @var $model app\modules\common\models\MercadoUniversidade */
/* @var $trotes array */
/* @var $mercados array */
/* @var $universidades array */

$this->title = 'Novo vínculo entre mercado e universidade';
$this->params['breadcrumbs'][] = ['label' => 'Vínculos de mercados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            Selecione o trote, o mercado e a universidade que poderá utilizá-lo como parceiro.
        </p>

        <?= $this->render('_form', [
            'model' => $model,
            'trotes' => $trotes,
            'mercados' => $mercados,
            'universidades' => $universidades,
        ]) ?>
    </div>
</div>
