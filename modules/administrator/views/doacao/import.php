<?php

use app\modules\participante\models\Trote;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <title>Importador de Academicos em massa</title>
</head>

<body>
    <div class="container-fluid">
        <!-- Page Heading -->

        <h1>Importar Excel CSV</h1>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'file')->label('Planilha Excel:')->fileInput(['class' => 'form-control']) ?>

        <?= $form->field($model, 'comprovante')->label('Comprovante:')->fileInput(['class' => 'form-control']) ?>

        <?=
        $form->field($model, 'trote_id')->label('Evento')->widget(Select2::classname(), [
            'options' => ['placeholder' => '- Selecione uma opção -'],
            'data' => ArrayHelper::map(Trote::find()->all(), 'id', 'nome'),
        ]);
        ?>

        <?=
        $form->field($model, 'tipo_doacao')->label('Tipo de Doação')->widget(Select2::classname(), [
            'options' => ['placeholder' => '- Selecione uma opção -'],
            'data' => [
                'Alimentos' => 'Alimentos',
                'Comissão' => 'Comissão',
                'Participação Presencial' => 'Participação Presencial',
                'Sangue' => 'Sangue',
                'Medula Óssea' => 'Medula Óssea',
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
        <input type="submit" name="enviar">

    </div>
    <?php ActiveForm::end(); ?>
</body>

</html>