<?php

use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Usuários';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Usuários', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Usuários', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de usuários</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Novo usuário', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
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
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->getStatusBadge();
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => \app\models\User::getStatusList(),
                    'filterInputOptions' => ['placeholder' => 'Status'],
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],
                'created_at:datetime',
                [
                    'class' => '\\kartik\\grid\\ActionColumn',
                    'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                    'buttons' => [
                        'delete' => static function ($url, $model) {
                            return Html::a('<i class="fas fa-trash" aria-hidden="true"></i>', $url, [
                                'class' => 'btn btn-sm btn-outline-danger',
                                'title' => 'Inativar usuário',
                                'aria-label' => 'Inativar ' . $model->nome,
                                'data-method' => 'post',
                                'data-confirm' => 'Deseja inativar o usuário "' . $model->nome . '"?',
                                'data-pjax' => '0',
                            ]);
                        },
                        'update' => static function ($url, $model) {
                            return Html::a('<i class="fas fa-pencil-alt" aria-hidden="true"></i>', $url, [
                                'class' => 'btn btn-sm btn-outline-primary',
                                'title' => 'Editar usuário',
                                'aria-label' => 'Editar ' . $model->nome,
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
