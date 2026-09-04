<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mercados parceiros';

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="container-fluid">

    <div class="card shadow mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h1 class="h4 mb-0">
                    <?= Html::encode($this->title) ?>
                </h1>

                <?= Html::a(
                    'Cadastrar mercado',
                    ['create'],
                    ['class' => 'btn btn-success']
                ) ?>

            </div>

            <?php if (Yii::$app->session->hasFlash('success')): ?>

                <div class="alert alert-success" role="alert">
                    <?= Html::encode(
                        Yii::$app->session->getFlash('success')
                    ) ?>
                </div>

            <?php endif; ?>

            <?php if (Yii::$app->session->hasFlash('error')): ?>

                <div class="alert alert-danger" role="alert">
                    <?= Html::encode(
                        Yii::$app->session->getFlash('error')
                    ) ?>
                </div>

            <?php endif; ?>

            <?= GridView::widget([

                'dataProvider' => $dataProvider,

                'summary' => 'Exibindo {begin}-{end} de {totalCount} mercados parceiros.',

                'emptyText' => 'Nenhum mercado parceiro cadastrado.',

                'tableOptions' => [
                    'class' => 'table table-striped table-hover mb-0'
                ],

                'columns' => [

                    [
                        'class' => 'yii\grid\SerialColumn'
                    ],

                    'nome_mercado',

                    'endereco',

                    'numero',

                    'bairro',

                    [
                        'class' => 'yii\grid\ActionColumn',

                        'template' => '{delete}',

                        'headerOptions' => [
                            'style' => 'width: 90px;'
                        ],

                        'buttons' => [

                            'delete' => function ($url, $model) {

                                return Html::a(
                                    '<i class="fas fa-trash-alt" aria-hidden="true"></i>',

                                    [
                                        'delete',
                                        'id' => $model->id
                                    ],

                                    [
                                        'class' => 'btn btn-sm btn-danger',

                                        'title' => 'Excluir mercado',

                                        'aria-label' =>
                                            'Excluir ' . $model->nome_mercado,

                                        'data' => [

                                            'confirm' =>
                                                'Deseja realmente excluir o mercado "' .
                                                $model->nome_mercado .
                                                '"?',

                                            'method' => 'post',

                                        ],

                                    ]
                                );

                            },

                        ],

                    ],

                ],

            ]) ?>

        </div>

    </div>

</div>