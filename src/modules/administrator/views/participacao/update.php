<?php

use yii\helpers\Html;

$this->title = 'Editar participacao';
$this->params['breadcrumbs'][] = ['label' => 'Participacoes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="p-3">
                    <?= $this->render('_form', compact('model', 'users', 'trotes', 'universidades')) ?>
                </div>
            </div>
        </div>
    </div>
</div>
