<?php

use kartik\alert\Alert;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\participante\models\Universidade */

$this->title = 'Update Universidade: ' . $model->id;
?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <?php
        if ($success) {
            echo Alert::widget([
                'type' => Alert::TYPE_SUCCESS,
                'title' => 'Universidade',
                'icon' => 'fas fa-ok-circle',
                'body' => $msg,
                'showSeparator' => true,
                'delay' => 4000
            ]);
        }
        if ($error) {
            echo Alert::widget([
                'type' => Alert::TYPE_DANGER,
                'title' => 'Universidade',
                'icon' => 'fas fa-ok-circle',
                'body' => $msg,
                'showSeparator' => true,
                'delay' => 4000
            ]);
        }
        ?>
    </div>
    <!-- Color System -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    Atualizar Universidade
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