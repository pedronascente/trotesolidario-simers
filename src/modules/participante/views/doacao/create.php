<?php

Yii::$app->language = 'pt-BR';
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">Nova Doação</div>
                <div class="p-3">
                    <?= $this->render('_form', [
                        'model' => $model,
                        'participacoes' => $participacoes,
                        'tipoDoacao' => $tipoDoacao,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>