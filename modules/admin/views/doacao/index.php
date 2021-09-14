<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Doações</h1>
    </div>
    <!-- Color System -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?= Html::a('Criar Doação', ['create'], ['class' => 'btn btn-success']) ?>
                    </h6>
                </div>
                <div class="p-3"> 
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'arquivo',
                                'format' => 'raw',
                                'hAlign' => 'center',
                                'vAlign' => 'center',
                                'filter' => false,
                                'value' => function($model) {
                                    return Html::img("/imagens/doacoes/$model->arquivo", ["style" => "height: 80px;width: auto;"]);
                                }
                            ],
                            [
                                'attribute' => 'instituicao',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    'UFRGS - Universidade Federal do Rio Grande do Sul' => 'UFRGS - Universidade Federal do Rio Grande do Sul',
                                    'UFCSPA - Universidade Federal de Ciências da Saúde de POA' => 'UFCSPA - Universidade Federal de Ciências da Saúde de POA',
                                    'PUCRS - Pontifícia Universidade Católica do RGS' => 'PUCRS - Pontifícia Universidade Católica do RGS',
                                    'ULBRA - Universidade Luterana do Brasil' => 'ULBRA - Universidade Luterana do Brasil',
                                    'UNISINOS - Universidade do Vale do Rio dos Sinos' => 'UNISINOS - Universidade do Vale do Rio dos Sinos',
                                    'FEEVALE' => 'FEEVALE',
                                    'UCS - Universidade de Caxias do Sul' => 'UCS - Universidade de Caxias do Sul',
                                    'UPF - Universidade de Passo Fundo' => 'UPF - Universidade de Passo Fundo',
                                    'IMED - Faculdade Meridional' => 'IMED - Faculdade Meridional',
                                    'UFFS - Universidade Federal da Fronteira do Sul' => 'UFFS - Universidade Federal da Fronteira do Sul',
                                    'UFPEL - Universidade Federal de Pelotas' => 'UFPEL - Universidade Federal de Pelotas',
                                    'UCPEL - Universidade Católica de Pelotas' => 'UCPEL - Universidade Católica de Pelotas',
                                    'FURG - Universidade Federal de Rio Grande' => 'FURG - Universidade Federal de Rio Grande',
                                    'UFSM - Universidade Federal de Santa Maria' => 'UFSM - Universidade Federal de Santa Maria',
                                    'UFN - Universidade Franciscana' => 'UFN - Universidade Franciscana',
                                    'UNIVATES - Fundação Vale do Taquari' => 'UNIVATES - Fundação Vale do Taquari',
                                    'UNISC - Universidade de Santa Cruz' => 'UNISC - Universidade de Santa Cruz',
                                    'UNIPAMPA - Universidade Federal do Pampa' => 'UNIPAMPA - Universidade Federal do Pampa',
                                    'URI - Universidade Regional Integrada do Alto Uruguai e das Missões' => 'URI - Universidade Regional Integrada do Alto Uruguai e das Missões',
                                    'UNIJUÍ - Universidade Regional do Noroeste do Estado do Rio Grande do Sul' => 'UNIJUÍ - Universidade Regional do Noroeste do Estado do Rio Grande do Sul'
                                ],
                                'filterInputOptions' => ['placeholder' => '- Instituição -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'attribute' => 'validado',
                                'label' => 'Validados',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    'Sim' => 'Sim',
                                    'Não' => 'Não'
                                ],
                                'filterInputOptions' => ['placeholder' => '- Validados -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'attribute' => 'tipo_doacao',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    'Sangue' => 'Sangue',
                                    'Alimentos' => 'Alimentos'
                                ],
                                'filterInputOptions' => ['placeholder' => '- Tipo de Doação -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'attribute' => 'trote',
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    '2021/02' => '2021/02',
                                ],
                                'filterInputOptions' => ['placeholder' => '- Tipo de Doação -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'attribute' => 'ativo',
                                'value' => function($model) {
                                    return ($model->ativo == "1") ? "Ativo" : "Inativo";
                                },
                                'filterType' => GridView::FILTER_SELECT2,
                                'filter' => [
                                    '1' => 'Ativo',
                                    '0' => 'Inativo',
                                ],
                                'filterInputOptions' => ['placeholder' => '- Status -'],
                                'filterWidgetOptions' => ['pluginOptions' => ['allowClear' => true]],
                            ],
                            [
                                'class' => '\kartik\grid\ActionColumn',
                                'template' => '{update} {delete}',
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

