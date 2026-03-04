<?php

use kartik\alert\Alert;

/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Universidade */

?>

<div class="container-fluid">

    <!-- Page Heading -->


    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header  py-3">
                    Criar trote
                </div>
                <div class="p-3">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

</div>