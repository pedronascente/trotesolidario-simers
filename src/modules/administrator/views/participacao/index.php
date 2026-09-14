<?php

use app\models\User;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Participações';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .btn-group-actions { display: flex; gap: 6px; justify-content: center; align-items: center; }
</style>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Participações', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Participações', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de participações</h6>
        <div>
            <?= Html::a('Solicitações de correção', ['solicitacoes-correcao-universidade'], ['class' => 'btn btn-outline-warning btn-sm mr-2']) ?>
            <?= Html::a('<i class="fas fa-plus mr-1"></i> Nova participação', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
        </div>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,
            'hover' => true,
            'responsive' => true,
            'columns' => [
                ['class' => 'yii\\grid\\SerialColumn'],
                [
                    'attribute' => 'user_nome',
                    'label' => 'Usuario',
                    'value' => static fn($model) => $model->user ? $model->user->nome : '-',
                    'filter' => ArrayHelper::map(User::find()->orderBy(['nome' => SORT_ASC])->all(), 'nome', 'nome'),
                ],
                [
                    'attribute' => 'user_email',
                    'label' => 'E-mail',
                    'value' => static fn($model) => $model->user->email ?? '-',
                    'filter' => false,
                ],
                [
                    'attribute' => 'trote_edicao',
                    'label' => 'Trote',
                    'value' => static fn($model) => $model->trote ? (($model->trote->titulo ?: 'Sem titulo') . ' | ' . ($model->trote->edicao ?: '-')) : '-',
                    'filter' => ArrayHelper::map(Trote::find()->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])->all(), 'edicao', 'edicao'),
                ],
                [
                    'attribute' => 'universidade_nome',
                    'label' => 'Universidade',
                    'value' => static fn($model) => $model->universidade->nome ?? '-',
                    'filter' => ArrayHelper::map(Universidade::find()->orderBy(['nome' => SORT_ASC])->all(), 'nome', 'nome'),
                ],
                'curso',
                    [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->getStatusBadge();
                    },
                    'filterType' => GridView::FILTER_SELECT2,
                    'filter' => \app\modules\common\models\Participacao::getStatusList(),
                    'filterInputOptions' => ['placeholder' => 'Status'],
                    'filterWidgetOptions' => [
                        'pluginOptions' => ['allowClear' => true],
                    ],
                ],

                [
                    'attribute' => 'created_at',
                    'format' => ['datetime', 'php:d/m/Y H:i'],
                    'filter' => false,
                ],
                [
                    'class' => '\\kartik\\grid\\ActionColumn',
                    'template' => '<div class="btn-group-actions">{update} {delete}</div>',
                    'headerOptions' => ['style' => 'width:120px'],
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<i class="fas fa-pencil-alt" aria-hidden="true"></i>', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-primary',
                                'title' => 'Editar participação',
                                'aria-label' => 'Editar participação',
                                'data-pjax' => '0',
                            ]);
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="fas fa-trash-alt" aria-hidden="true"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-danger',
                                'title' => 'Excluir participação',
                                'aria-label' => 'Excluir participação',
                                'data' => [
                                    'confirm' => 'Deseja realmente excluir esta participação?',
                                    'method' => 'post',
                                    'pjax' => '0',
                                ],
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>