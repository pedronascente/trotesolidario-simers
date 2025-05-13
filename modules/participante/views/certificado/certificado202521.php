<?php

use app\modules\participante\models\Helper;
use yii\helpers\Url;

$tipo = '';
$horasAtual = 0;

if ($model['tipo_doacao'] == 'Comissão' || $model['tipo_doacao'] == 'Comissão organizadora') {
    $horasAtual = 40;
    $tipo = ' na qualidade de MEMBRO DA COMISSÃO ORGANIZADORA';
} else if ($model['tipo_doacao'] == 'Sangue' || $model['tipo_doacao'] == 'Medula Óssea') {
    $horasAtual = 8;
    $tipo = ' na qualidade de PARTICIPANTE';
} else if ($model['tipo_doacao'] == 'Alimentos') {
    $horasAtual = 4;
    $tipo = ' na qualidade de PARTICIPANTE';
} else if ($model['tipo_doacao'] == 'Participação Presencial') {
    $horasAtual = 6;
    $tipo = ' na qualidade de PARTICIPANTE';
} else {
    $horasAtual = 0;
    $tipo = ' ';
}

$totalHoras = isset($model['total_horas']) ? $model['total_horas'] : $horasAtual;

$doacoesTexto = '';
if (isset($model['all_donations']) && !empty($model['all_donations'])) {
    $doacoesTexto = implode(', ', $model['all_donations']);
    $doacoesTexto = " realizando doações do tipo: {$doacoesTexto},";
}

$model['frase_certificado'] .= " {$totalHoras} horas.";
?>
<style type="text/css">
    .tg {}

    .tg td {
        font-family: AvenirLTStd-Roman;
        font-size: 14px;
        overflow: hidden;
        padding: 10px 5px;
        word-break: normal;
    }

    .tg th {
        font-family: AvenirLTStd-Roman;
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
    <div style="background-color:#520EBA; background-repeat: no-repeat; background-size: cover;background-image: url('<?= Url::to('@web/img/bg-certificado-2025_resized.png') ?>');">
        <div class="well"
            style="background-color: transparent; border:none;height: 530px;">
            <div class="row" style="padding:20px">
                <table class="tg" style="table-layout: fixed; width: 100%">
                    <thead>
                        <tr>
                            <th class="tg-0lax"></th>
                            <th class="tg-baqh" colspan="3">
                                <img src="<?= Url::to('@web/img/logo-site-2025.png') ?>" style="width:auto;height: 130px;"
                                    alt="logo" />
                            </th>
                            <th class="tg-0lax"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="padding-top:20px;">
                            <td class="tg-0lax"></td>
                            <td class="tg-baqh" colspan="3" style="text-align: center;">
                                <h1 style="color:#2e056b; ">CERTIFICADO</h1>
                            </td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-baqh" colspan="3" style="text-align: center;" rowspan="2">
                                <p style="color: #2e056b; font-size: 22px; padding">Certificamos que <b><?= $model['name'] ?></b>,
                                    participou do Trote Solidário <?= $model['trote'] ?>,<?= $tipo ?>, na qualidade de voluntário(a)<?= $doacoesTexto ?>
                                    promovido pelo Núcleo Acadêmico Simers, <?= $model['frase_certificado'] ?></p>
                            </td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr style="padding-top:20px;">
                            <td class="tg-0lax"></td>
                            <td class="tg-0lax"></td>
                            <th class="tg-baqh"
                                style="  flex-direction: row; justify-content: start; text-align: left;">
                                <img src="<?= Url::to('@web/img/assinatura-marcia.png') ?>"
                                    style="width:auto;height: 80px;" alt="logo" />
                            </th>
                            <th class="tg-baqh" style="flex-direction: row; justify-content: end; text-align: center;">
                                <img src="<?= Url::to('@web/img/assinatura-marcelo.png') ?>"
                                    style="width:auto;height: 80px;" alt="logo" />

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