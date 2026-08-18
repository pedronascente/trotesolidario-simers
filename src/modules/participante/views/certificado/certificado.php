<?php

use yii\helpers\Html;

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
$assinaturasPath = $asset('assinaturascertificado.jpg');
$participacaoPath = $asset('participacaocertificado.jpg');
$nome = Html::encode((string) ($model['name'] ?? '-'));
$trote = Html::encode((string) ($model['trote'] ?? '-'));
$frase = Html::encode((string) ($model['frase_certificado'] ?? ''));
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
                        <td class="tg-baqh" colspan="3" style="text-align:center;">
                            <h1 style="color:#fff;">CERTIFICADO</h1>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align:center;" rowspan="2">
                            <p style="color:#fff; font-size:22px;">Certificamos que <b><?= $nome ?></b>, participou do Trote Solid&aacute;rio <?= $trote ?>, promovido pelo N&uacute;cleo Acad&ecirc;mico Simers, <?= $frase ?></p>
                        </td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <th class="tg-baqh" colspan="3">
                            <?php if ($assinaturasPath !== ''): ?>
                                <img src="<?= $assinaturasPath ?>" style="width:auto; height:80px;" alt="Assinaturas" />
                            <?php endif; ?>
                        </th>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <th class="tg-baqh" colspan="3">
                            <?php if ($participacaoPath !== ''): ?>
                                <img src="<?= $participacaoPath ?>" style="width:auto; height:80px;" alt="Participação" />
                            <?php endif; ?>
                        </th>
                        <td class="tg-0lax"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
