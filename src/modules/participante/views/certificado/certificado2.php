<?php

$renderMode = isset($renderMode) && $renderMode === 'pdf' ? 'pdf' : 'web';

$asset = static function (string $fileName) use ($renderMode): string {
    $path = null;

    foreach (['@webroot', '@app/web'] as $alias) {
        $basePath = Yii::getAlias($alias, false);
        if (!is_string($basePath) || $basePath === '') {
            continue;
        }

        $candidate = realpath(rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fileName);
        if ($candidate !== false && is_file($candidate)) {
            $path = $candidate;
            break;
        }
    }

    if ($path === null) {
        return '';
    }

    if ($renderMode === 'pdf') {
        $data = file_get_contents($path);
        if ($data === false) {
            return '';
        }

        return 'data:image/jpeg;base64,' . base64_encode($data);
    }

    return Yii::getAlias('@web') . '/img/' . rawurlencode($fileName);
};

$logoPath = $asset('logocertificado.jpg');
$nucleoPath = $asset('nucleoacademicoverde.jpg');
$textoProjeto = 'O Trote Solid&aacute;rio &eacute; um projeto realizado pelo N&uacute;cleo Acad&ecirc;mico Simers desde 2008. Esta a&ccedil;&atilde;o &eacute; a uni&atilde;o da campanha de doa&ccedil;&atilde;o de sangue realizada pelos ingressantes das universidades de medicina e o convite &agrave; sociedade para doar alimentos &agrave;s comunidades carentes, que mant&eacute;m a tradi&ccedil;&atilde;o do trote universit&aacute;rio, tornando realidade o objetivo de todos os m&eacute;dicos: SALVAR VIDAS.';
$textoReconhecimento = 'Como reconhecimento p&uacute;blico, o Trote Solid&aacute;rio foi vencedor do Pr&ecirc;mio Top Cidadania 2013 da ABRH-RS e do Pr&ecirc;mio Ser Humano Oswaldo Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustent&aacute;vel e Responsabilidade Social/ Organiza&ccedil;&atilde;o Cidad&atilde;.';
$textoAtual = 'Atualmente o Trote Solid&aacute;rio promove as seguintes a&ccedil;&otilde;es: doa&ccedil;&atilde;o de sangue, coleta de alimentos, tampinhas pl&aacute;sticas e livros pr&eacute;-vestibular para doa&ccedil;&atilde;o junto a entidades carentes.';
?>
<style type="text/css">
    .tg td,
    .tg th {
        font-family: Arial, sans-serif;
        font-size: 14px;
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
                            <?php if ($logoPath !== ''): ?>
                                <img src="<?= $logoPath ?>" style="width:auto; height:180px;" alt="Logo do certificado" />
                            <?php endif; ?>
                        </th>
                        <th class="tg-0lax"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align:justify;">
                            <p style="color:#fff; font-size:18px;"><?= $textoProjeto ?></p>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align:justify;">
                            <p style="color:#fff; font-size:18px;"><?= $textoReconhecimento ?></p>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align:justify;">
                            <p style="color:#fff; font-size:18px;"><?= $textoAtual ?></p>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <th class="tg-baqh" colspan="3">
                            <?php if ($nucleoPath !== ''): ?>
                                <img src="<?= $nucleoPath ?>" alt="N&uacute;cleo Acad&ecirc;mico" />
                            <?php endif; ?>
                        </th>
                        <td class="tg-0lax"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
