<?php

$logoPath = realpath(Yii::getAlias('@webroot') . '/img/logocertificado.jpg');
$nucleoPath = realpath(Yii::getAlias('@webroot') . '/img/nucleoacademicoverde.jpg');
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
        vertical-align: top;
    }

    .tg .tg-0lax {
        text-align: left;
        vertical-align: top;
    }
</style>

<body class="body">
    <div class="well" style="background-color:#2b9879;">
        <div class="row">
            <table class="tg" style="table-layout:fixed; width:100%;">
                <thead>
                    <tr>
                        <th class="tg-0lax"></th>
                        <th class="tg-baqh" colspan="3">
                            <?php if ($logoPath): ?>
                                <img src="<?= $logoPath ?>" style="width:auto; height:180px;" alt="logo" />
                            <?php endif; ?>
                        </th>
                        <th class="tg-0lax"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align:justify;">
                            <p style="color:#fff; font-size:18px;">O Trote Solid?rio ? um projeto realizado pelo N?cleo Acad?mico Simers desde 2008. Esta a??o ? a uni?o da campanha de doa??o de sangue realizada pelos ingressantes das universidades de medicina e o convite a sociedade para doar alimentos ?s comunidades carentes, que mant?m a tradi??o do trote universit?rio, tornando realidade o objetivo de todos os m?dicos: SALVAR VIDAS.</p>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align:justify;">
                            <p style="color:#fff; font-size:18px;">Como reconhecimento p?blico, o Trote Solid?rio foi vencedor do Pr?mio Top Cidadania 2013 da ABRH-RS e do Pr?mio Ser Humano Oswaldo Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustent?vel e Responsabilidade Social/ Organiza??o Cidad?.</p>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align:justify;">
                            <p style="color:#fff; font-size:18px;">Atualmente o Trote Solid?rio promove as seguintes a??es: doa??o de sangue, coleta de alimentos, tampinhas pl?sticas e livros pr?-vestibular para doa??o junto a entidades carentes.</p>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <th class="tg-baqh" colspan="3">
                            <?php if ($nucleoPath): ?>
                                <img src="<?= $nucleoPath ?>" alt="nucleo academico" />
                            <?php endif; ?>
                        </th>
                        <td class="tg-0lax"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
