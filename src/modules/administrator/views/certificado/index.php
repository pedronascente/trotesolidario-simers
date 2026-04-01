<?php

use kartik\alert\Alert;
use kartik\grid\GridView;
use yii\helpers\Html;

$this->title = 'Certificados';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_SUCCESS, 'title' => 'Certificados', 'icon' => 'fas fa-check-circle', 'body' => Yii::$app->session->getFlash('success'), 'showSeparator' => true, 'delay' => 4000]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <?= Alert::widget(['type' => Alert::TYPE_DANGER, 'title' => 'Certificados', 'icon' => 'fas fa-times-circle', 'body' => Yii::$app->session->getFlash('error'), 'showSeparator' => true, 'delay' => 5000]) ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h1 class="h5 mb-0"><?= Html::encode($this->title) ?></h1>
                    <span class="text-muted small">Lista de certificados gerados e encaminhamento por e-mail.</span>
                </div>
                <div class="p-3">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'hover' => true,
                        'responsive' => true,
                        'pjax' => true,
                        'columns' => [
                            'id',
                            [
                                'label' => 'Trote',
                                'value' => static function ($model) {
                                    return $model->troteDescricao;
                                },
                            ],
                            [
                                'label' => 'Evento(s)',
                                'value' => static function ($model) {
                                    return $model->eventoNomes;
                                },
                                'contentOptions' => ['style' => 'max-width: 260px; white-space: normal;'],
                            ],
                            [
                                'attribute' => 'participante_nome',
                                'label' => 'Participante',
                                'value' => static function ($model) {
                                    return $model->participanteNome;
                                },
                            ],
                            [
                                'attribute' => 'participante_email',
                                'label' => 'E-mail',
                                'format' => 'email',
                                'value' => static function ($model) {
                                    return $model->participanteEmail;
                                },
                            ],
                            [
                                'attribute' => 'codigo_validador',
                                'label' => 'Codigo',
                            ],
                            [
                                'attribute' => 'carga_horaria_total',
                                'label' => 'Carga horaria',
                                'value' => static function ($model) {
                                    return $model->carga_horaria_total . ' h';
                                },
                            ],
                            [
                                'attribute' => 'data_emissao',
                                'label' => 'Emissao',
                                'format' => ['datetime', 'php:d/m/Y H:i'],
                            ],
                            [
                                'class' => '\\kartik\\grid\\ActionColumn',
                                'template' => '{view} {send-email}',
                                'buttons' => [
                                    'view' => static function ($url, $model) {
                                        return Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-info',
                                            'title' => 'Visualizar certificado',
                                            'target' => '_blank',
                                            'data-pjax' => '0',
                                        ]);
                                    },
                                    'send-email' => static function ($url, $model) {
                                        return Html::a('<i class="fas fa-paper-plane"></i>', ['send-email', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-primary',
                                            'title' => 'Encaminhar certificado por e-mail',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Deseja encaminhar este certificado por e-mail para o participante?',
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
