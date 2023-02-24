<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\participante\models\Helper;
use app\modules\participante\models\Users;
use app\modules\participante\models\Trote;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\modules\participante\models\Universidade;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\UsersSearchModel */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<?php
$gridColumns = [
    'name',
    'email:email',
    [
        'attribute' => 'estudante',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ["Sim" => 'Sim', "Não" => 'Não'],
        'filterInputOptions' => ['placeholder' => 'Status'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
    ],
    [
        'attribute' => 'instituicao',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => ArrayHelper::map(Universidade::find()->where(['ativo' => '1'])->all(), 'id', 'nome'),
        'filterInputOptions' => ['placeholder' => '- Instituição -'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
        'value' => function ($model) {
            $return = Universidade::find()->where(['id' => $model->instituicao])->one();
            return ($return) ? $return->nome : "Não definido";
        }
    ],
    'telefone',
    'previsaoFormatura',
    'conheceONas',
    [
        'attribute' => 'trote_id',
        'label' => 'Trote',
        'value' => function ($model) {
            $trote = Trote::find()->where(['id' => $model->trote_id])->one();

            return ($trote) ? $trote->nome : "";
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [1 => 'Ativo', 0 => 'Inativo'],
        'filterInputOptions' => ['placeholder' => 'Status'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
    ],
    [
        'attribute' => 'status',
        'value' => function ($model) {
            return ($model->status == Users::STATUS_ACTIVE) ? "Ativo" : "Inativo";
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [1 => 'Ativo', 0 => 'Inativo'],
        'filterInputOptions' => ['placeholder' => 'Status'],
        'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
    ],
];
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"> Usuários</h1>
    </div>
    <!-- Color System -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="p-3">
                    <p>
                        <?=
                        Html::a('Doações Usuários', 'doacao-users', ['class' => 'btn btn-md btn-success'])
                        ?>
                    </p>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'headerContainer' => ['style' => 'top:50px', 'class' => 'kv-table-header'], // offset from top
                        //'floatHeader' => true, // table header floats when you scroll
                        //'floatPageSummary' => true, // table page summary floats when you scroll
                        //'floatFooter' => false, // disable floating of table footer
                        'pjax' => true, // pjax is set to always false for this demo
                        // parameters from the demo form
                        //  'responsive' => true,
                        //'bordered' => true,
                        //'striped' => true,
                        //'condensed' => true,
                        'hover' => true,
                        //'showPageSummary' => true,
                        'panel' => [
                            'heading' => '<i class="fa fa-book"></i>  Usuários',
                            'type' => 'success',
                            'before' => '<div style="padding-top: 7px;"><em></em></div>',
                        ],
                        // set export properties
                        'export' => [
                            'fontAwesome' => true
                        ],
                        'exportConfig' => [
                            'html' => [],
                            'csv' => [],
                            'txt' => [],
                            'xls' => [],
                            //'pdf' => [],
                            'json' => [],
                        ],
                        'columns' => $gridColumns,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>