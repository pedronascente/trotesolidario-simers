<?php


use yii\helpers\Html;
use kartik\alert\Alert;

Yii::$app->language = "pt-BR";
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\TipoDoacao */

$this->title = 'Criar Tipo de Doação';
$this->params['breadcrumbs'][] = ['label' => 'Tipos Doação', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
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
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header  py-3">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="p-3">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>