<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Perfil do usuario: ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Perfil';
?>

<div class="user-perfil container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <span>Dados do usuario</span>
                    <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                </div>
                <div class="p-3">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
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
                                'attribute' => 'role',
                                'value' => static function ($model) {
                                    return \app\models\User::getRoleList()[$model->role] ?? $model->role;
                                },
                            ],
                            [
                                'attribute' => 'status',
                                'value' => static function ($model) {
                                    return \app\models\User::getStatusList()[$model->status] ?? $model->status;
                                },
                            ],
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    Dados do participante
                </div>
                <div class="p-3">
                    <?php if ($participante): ?>
                        <?= DetailView::widget([
                            'model' => $participante,
                            'attributes' => [
                                'id',
                                [
                                    'attribute' => 'estudante',
                                    'value' => static function ($model) {
                                        return $model->estudante ? 'Sim' : 'Nao';
                                    },
                                ],
                                'previsao_formatura:datetime',
                                [
                                    'attribute' => 'estudante_medicina',
                                    'value' => static function ($model) {
                                        return $model->estudante_medicina ? 'Sim' : 'Nao';
                                    },
                                ],
                            ],
                        ]) ?>
                        <?= Html::a('Editar participante', ['participante/update', 'id' => $participante->id], ['class' => 'btn btn-primary btn-sm']) ?>
                    <?php else: ?>
                        <p class="text-muted mb-3">Este usuario nao possui perfil de participante.</p>
                        <?= Html::a('Criar participante', ['participante/create', 'user_id' => $model->id], ['class' => 'btn btn-success btn-sm']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
