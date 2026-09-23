<?php

use kartik\alert\Alert;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\User */

$this->title = 'Novo usuário';
$this->params['breadcrumbs'][] = ['label' => 'Usuários', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Usuários',
        'icon' => 'fas fa-times-circle',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 5000,
    ]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="p-3"><?= $this->render('_form', ['model' => $model]) ?></div>
</div> 