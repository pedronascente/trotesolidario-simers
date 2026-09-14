<?php

use yii\helpers\Html;
use kartik\alert\Alert;
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\trote */

$this->title = 'Atualizar Trote';  
$this->params['breadcrumbs'][] = ['label' => 'Trotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'title' => 'trote',
        'icon' => 'fas fa-check-circle',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => 4000,
    ]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'trote',
        'icon' => 'fas fa-times-circle',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 4000,
    ]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="p-3"><?= $this->render('_form', ['model' => $model]) ?></div>
</div> 