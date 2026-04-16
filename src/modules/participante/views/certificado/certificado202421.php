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

        $candidate = realpath($basePath . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fileName);
        if ($candidate !== false && is_file($candidate)) {
            $path = $candidate;
            break;
        }
    }

    if ($path === null) {
        return '';
    }

    if ($renderMode === 'pdf') {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };

        $data = file_get_contents($path);
        if ($data === false) {
            return '';
        }

        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }

    return Yii::getAlias('@web') . '/img/' . rawurlencode($fileName);
};

$normalize = static function (?string $value): string {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    if (!mb_check_encoding($value, 'UTF-8')) {
        $converted = @mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252');
        if (is_string($converted) && $converted !== '') {
            $value = $converted;
        }
    }

    return $value;
};

$nome = $normalize($model['name'] ?? '-');
$trote = $normalize($model['trote'] ?? '-');
$qualidade = $normalize($model['qualidade'] ?? 'PARTICIPANTE');
$totalHoras = (int) ($model['total_horas'] ?? 0);
$fraseCertificado = $normalize($model['frase_certificado'] ?? '');

$doacoes = [];
foreach (($model['all_donations'] ?? []) as $doacao) {
    $doacao = $normalize($doacao);
    if ($doacao !== '') {
        $doacoes[] = $doacao;
    }
}

$tiposDoacao = implode(', ', $doacoes);

$textoPrincipal = 'Certificamos que <b>' . Html::encode($nome) . '</b>, participou do Trote Solidário '
    . Html::encode($trote)
    . ', na qualidade de ' . Html::encode($qualidade)
    . ', como voluntário(a)';

if ($tiposDoacao !== '') {
    $textoPrincipal .= ', realizando doações do tipo: ' . Html::encode($tiposDoacao);
}

$textoPrincipal .= ', promovido pelo Núcleo Acadêmico Simers';

if ($fraseCertificado !== '') {
    $textoPrincipal .= ', ' . Html::encode($fraseCertificado);
}

if ($totalHoras > 0) {
    $textoPrincipal .= ' ' . $totalHoras . ' horas';
}

$fundo = $asset('FUNDO.png');
$logo20242 = $asset('LOGO20242.png');
$AssinaturasNASvinicius = $asset('Assinaturas_NAS_vinicius.png');
$AssinaturasNASrovinski = $asset('Assinaturas_NAS_rovinski.png');
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
        <div class="well"
            style="background-repeat: no-repeat; background-size: cover;background-image: url('<?= $fundo ?>');background-color: #fefefe; border-radius:36px;">
            <div class="row">
                <table class="tg" style="table-layout: fixed; width: 100%">
                    <thead>
                        <tr>
                            <th class="tg-0lax"></th>
                            <th class="tg-baqh" colspan="3">
                                <img src="<?= $logo20242; ?>" style="width:auto;height: 130px;"
                                    alt="logo" />
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
                                <p style="color:#000;font-size: 22px;"><?= $textoPrincipal ?></p>
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
                                <img src="<?= $AssinaturasNASvinicius; ?>" style="width:auto;height: 80px;" alt="logo" />
                            </th>
                            <th class="tg-baqh" style="flex-direction: row; justify-content: end; text-align: center;">
                                <img src="<?= $AssinaturasNASrovinski; ?>" style="width:auto;height: 80px;" alt="logo" />
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
