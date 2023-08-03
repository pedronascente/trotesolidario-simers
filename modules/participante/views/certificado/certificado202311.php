<?php

use app\modules\participante\models\Helper;
use yii\helpers\Url;

$tipo = '';
if ($model['tipo_doacao'] == 'Comissão') {
    $model['frase_certificado'] .= ' 30 horas.';
    $tipo = ' na qualidade de MEMBRO DA COMISSÃO ORGANIZADORA';
} else if ($model['tipo_doacao'] == 'Sangue') {
    $model['frase_certificado'] .= ' 6 horas.';
    $tipo = ' na qualidade de PARTICIPANTE';
} else if ($model['tipo_doacao'] == 'Alimentos') {
    $model['frase_certificado'] .= ' 4 horas.';
    $tipo = ' na qualidade de PARTICIPANTE';
} else if ($model['tipo_doacao'] == 'Participação Presencial') {
    $model['frase_certificado'] .= ' 6 horas.';
    $tipo = ' na qualidade de PARTICIPANTE';
} else if ($model['tipo_doacao'] == 'Medula Óssea') {
    $model['frase_certificado'] .= ' 6 horas.';
    $tipo = ' na qualidade de PARTICIPANTE';
} else {
    $model['frase_certificado'] .= ' 0 horas.';
    $tipo = ' ';
}

?>
<style type="text/css">
    .tg {}

    .tg td {
        font-family: Arial, sans-serif;
        font-size: 14px;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg th {
        font-family: Arial, sans-serif;
        font-size: 14px;
        font-weight: normal;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg .tg-baqh {
        text-align: center;
        vertical-align: top
    }

    .tg .tg-0lax {
        text-align: left;
        vertical-align: top
    }
</style>

<body class="body">
    <div style="background-color: #4b3c91;padding: 40px 40px 30px 40px;">
        <div class="well" style="background-repeat: no-repeat; background-size: cover;background-image: url('<?= Url::to('@web/img/FUNDO.png') ?>');background-color: #fefefe; border-radius:36px;">
            <div class="row">
                <table class="tg" style="table-layout: fixed; width: 100%">
                    <thead>
                        <tr>
                            <th class="tg-0lax"></th>
                            <th class="tg-baqh" colspan="3">
                                <img src="<?= Url::to('@web/img/logo15trote.png') ?>" style="width:auto;height: 130px;" alt="logo" />
                            </th>
                            <th class="tg-0lax"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-baqh" colspan="3" style="text-align: center;">
                                <h1 style="color:#000;">CERTIFICADO</h1>
                            </td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-baqh" colspan="3" style="text-align: center;" rowspan="2">
                                <p style="color:#000;font-size: 22px;">Certificamos que <b><?= $model['name'] ?></b>, participou do Trote Solidário <?= $model['trote'] ?>,<?= $tipo ?>, promovido pelo Núcleo Acadêmico Simers, <?= $model['frase_certificado'] ?></p>
                            </td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-0lax"></td>
                            <th class="tg-baqh" style="  flex-direction: row; justify-content: start; text-align: left;">
                                <img src="<?= Url::to('@web/img/Assinaturas_NAS_vinicius.png') ?>" style="width:auto;height: 80px;" alt="logo" />
                            </th>
                            <th class="tg-baqh" style="flex-direction: row; justify-content: end; text-align: center;">
                                <img src="<?= Url::to('@web/img/Assinaturas_NAS_rovinski.png') ?>" style="width:auto;height: 80px;" alt="logo" />

                            </th>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <th class="tg-baqh" colspan="3">
                            </th>
                            <td class="tg-0lax"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>