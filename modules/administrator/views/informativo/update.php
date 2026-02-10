<?php

use kartik\alert\Alert;

?>
<div class="container-fluid">

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_SUCCESS,
            'title' => 'Informativo',
            'icon' => 'fas fa-check-circle',
            'body' => Yii::$app->session->getFlash('success'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget([
            'type' => Alert::TYPE_DANGER,
            'title' => 'Informativo',
            'icon' => 'fas fa-times-circle',
            'body' => Yii::$app->session->getFlash('error'),
            'showSeparator' => true,
            'delay' => 4000,
        ]) ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header  py-3">
                    Atualizar Informativo
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