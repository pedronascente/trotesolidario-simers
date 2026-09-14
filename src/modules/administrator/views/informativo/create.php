<?php
use yii\helpers\Html;
use kartik\alert\Alert;

Yii::$app->language = "pt-BR";
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Informativo */

$this->title = 'Novo informativo';
$this->params['breadcrumbs'][] = ['label' => 'Informativo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="d-sm-flex align-items-center justify-content-between">
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
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="card-body"><?= $this->render('_form', ['model' => $model,]) ?></div>
</div>