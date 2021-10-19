<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\admin\models\Helper;
use app\modules\admin\models\Users;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;
use app\modules\admin\models\Universidade;

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
        'value' => function($model) {
            $return = Universidade::find()->where(['id' => $model->instituicao])->one();
            return ($return) ? $return->nome : "Não definido";
        }
    ],
    'telefone',
    'previsaoFormatura',
    'conheceONas',
    [
        'attribute' => 'status',
        'value' => function($model) {
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
                    <?=
                    ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns,
                        'columnSelectorOptions' => [
                            'label' => 'Columns',
                        ],
                        'fontAwesome' => true,
                        'dropdownOptions' => [
                            'label' => 'Export All',
                        ]
                    ]);
                    ?>
                    <?=
                        Html::a('Doações Usuários','doacao-users',['class'=>'btn btn-md btn-success'])
                    ?>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => $gridColumns,
                    ]);
                    ?>


                </div>
            </div>
        </div>
    </div>
</div>

