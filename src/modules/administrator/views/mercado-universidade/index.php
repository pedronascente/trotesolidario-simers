<?php

use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mercados e universidades';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Vínculo de mercado', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Vínculo de mercado', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 4000]) ?>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Lista de vínculos entre mercados e universidades</h6>
        <?= Html::a('<i class="fas fa-plus mr-1"></i> Vincular mercado', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
    </div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'pjax' => true,
            'hover' => true,
            'responsive' => true,
            'columns' => [
                ['class' => 'yii\\grid\\SerialColumn'],
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'vAlign' => 'middle',
                    'headerOptions' => ['style' => 'width:80px'],
                ],
                [
                    'label' => 'Mercado',
                    'value' => static function ($model) {
                        if ($model->mercado === null) {
                            return '-';
                        }

                        return $model->mercado->nome_mercado . ' — ' . $model->mercado->endereco;
                    },
                    'vAlign' => 'middle',
                ],
                [
                    'label' => 'Universidade',
                    'value' => static function ($model) {
                        return $model->universidade->nome ?? '-';
                    },
                    'vAlign' => 'middle',
                ],
                [
                    'class' => 'kartik\\grid\\ActionColumn',
                    'header' => 'Ações',
                    'template' => '{delete}',
                    'headerOptions' => ['style' => 'width:90px'],
                    'contentOptions' => ['class' => 'text-center'],
                    'buttons' => [
                        'delete' => static function ($url, $model) {
                            $mercado = $model->mercado->nome_mercado ?? 'este mercado';
                            $universidade = $model->universidade->nome ?? 'esta universidade';

                            return Html::a('<i class="fas fa-unlink"></i>', ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger btn-sm',
                                'title' => 'Desvincular mercado',
                                'aria-label' => 'Desvincular mercado',
                                'data-confirm' => "Deseja remover o vínculo entre {$mercado} e {$universidade}?",
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
