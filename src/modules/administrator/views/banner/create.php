<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Banner */

$this->title = 'Criar Banner';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="p-3">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>