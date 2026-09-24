<?php

use yii\helpers\Html;
use yii\helpers\Json;
Yii::$app->language = 'pt-BR';
/* @var $this yii\web\View */
/* @var $model app\modules\common\models\Doacao */

$this->title = 'Nova Doação';  
$this->params['breadcrumbs'][] = ['label' => 'Doações', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card shadow mb-4">
    <div id="alerta-doacao-sangue" class="alert alert-warning  py-2 mb-2" role="alert" hidden>
        <strong><i class="fas fa-info-circle" aria-hidden="true"></i> Atenção:</strong>
        Não será aceita doação de terceiros, somente doação do próprio participante.
    </div>
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-success"><?= Html::encode($this->title) ?></h6></div>
    <div class="p-3">
        <?= $this->render("_form", ["model" => $model, "participacoes" => $participacoes, "tipoDoacao" => $tipoDoacao]) ?>
    </div>
</div>

<?php
$tipoDoacaoSelector = Json::htmlEncode("#" . Html::getInputId($model, "tipo_doacao_id"));
$alertaDoacaoSangueSelector = Json::htmlEncode("#alerta-doacao-sangue");

$this->registerJs(<<<JS
$($tipoDoacaoSelector).on("change", function () {
    var tipoDoacao = $(this).find("option:selected").text().toLocaleLowerCase("pt-BR");
    $($alertaDoacaoSangueSelector).prop("hidden", tipoDoacao.indexOf("sangue") === -1);
}).trigger("change");
JS, \yii\web\View::POS_READY, "alerta-doacao-sangue");
?>
