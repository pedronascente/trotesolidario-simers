<?php

use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Usuarios', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Usuarios', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h1 class="h5 mb-0"><?= Html::encode($this->title) ?></h1>
                    <?= Html::a('Criar usuario', ['create'], ['class' => 'btn btn-success']) ?>
                </div>
                <div class="p-3">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'hover' => true,
                        'responsive' => true,
                        'columns' => [
                            'id',
                            'nome',
                            'username',
                            [
                                'attribute' => 'cpf',
                                'value' => static function ($model) {
                                    return $model->cpfFormatado;
                                },
                            ],
                            'email:email',
                            [
                                'attribute' => 'has_participante',
                                'label' => 'Perfil participante',
                                'filter' => [1 => 'Sim', 0 => 'Nao'],
                                'value' => static function ($model) {
                                    return $model->participante ? 'Sim' : 'Nao';
                                },
                            ],
                            [
                                'attribute' => 'estudante',
                                'label' => 'Estudante',
                                'filter' => [1 => 'Sim', 0 => 'Nao'],
                                'value' => static function ($model) {
                                    if (!$model->participante) {
                                        return '-';
                                    }

                                    return (int) $model->participante->estudante === 1 ? 'Sim' : 'Nao';
                                },
                            ],
                            [
                                'attribute' => 'estudante_medicina',
                                'label' => 'Medicina',
                                'filter' => [1 => 'Sim', 0 => 'Nao'],
                                'value' => static function ($model) {
                                    if (!$model->participante) {
                                        return '-';
                                    }

                                    return (int) $model->participante->estudante_medicina === 1 ? 'Sim' : 'Nao';
                                },
                            ],
                            [
                                'attribute' => 'role',
                                'filter' => \app\models\User::getRoleList(),
                                'value' => static function ($model) {
                                    $roles = \app\models\User::getRoleList();
                                    return $roles[$model->role] ?? $model->role;
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'filter' => \app\models\User::getStatusList(),
                                'value' => static function ($model) {
                                    $statuses = \app\models\User::getStatusList();
                                    return $statuses[$model->status] ?? $model->status;
                                },
                            ],
                            'created_at:datetime',
                            [
                                'class' => '\\kartik\\grid\\ActionColumn',
                                'template' => '{update} {delete}',
                                'buttons' => [
                                    'delete' => static function ($url, $model) {
                                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                                            'class' => 'btn btn-sm btn-danger',
                                            'title' => 'Inativar',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Deseja inativar este usuario?',
                                            'data-pjax' => '0',
                                        ]);
                                    },
                                    'update' => static function ($url) {
                                        return Html::a('<i class="fas fa-pencil-alt"></i>', $url, [
                                            'class' => 'btn btn-sm btn-primary',
                                            'title' => 'Editar',
                                            'data-pjax' => '0',
                                        ]);
                                    },
                                ],
                                'headerOptions' => ['style' => 'width: 120px'],
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
