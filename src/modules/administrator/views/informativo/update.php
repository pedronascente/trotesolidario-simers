<?php

use kartik\alert\Alert;
use yii\helpers\Html;
$this->title = 'Editar informativo';
$this->params['breadcrumbs'][] = ['label' => 'Informativos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
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


 <div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="card-body"><?= $this->render('_form', ['model' => $model]) ?></div>
</div>