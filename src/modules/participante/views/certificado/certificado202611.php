<?php

use yii\helpers\Html;
$renderMode = isset($renderMode) && $renderMode === 'pdf' ? 'pdf' : 'web';

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

$asset = static function (string $fileName): string {
    $path = realpath(Yii::getAlias('@webroot') . '/img/' . $fileName);
    if ($path === false || !is_file($path)) {
        return '';
    }

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
};

$logo = $asset('logo-site-2025.png');
$bg = $asset('bg-certificado-2025_resized.png');
$elementLeft = $asset('element-left.png');
$elementRight = $asset('element-right.png');
$assinaturaMarcia = $asset('assinatura-marcia.png');
$assinaturaMarcelo = $asset('assinatura-marcelo.png');

$nome = $normalize($model['name'] ?? '-');
$trote = $normalize($model['trote'] ?? '-');
$qualidade = mb_strtoupper($normalize($model['qualidade'] ?? 'PARTICIPANTE'), 'UTF-8');
$totalHoras = (int) ($model['total_horas'] ?? 0);
$fraseBase = $normalize($model['frase_certificado'] ?? '');
$fraseCertificado = trim($fraseBase . ' ' . $totalHoras . ' horas.');

$doacoes = [];
foreach (($model['all_donations'] ?? []) as $doacao) {
    $doacao = $normalize($doacao);
    if ($doacao !== '') {
        $doacoes[] = $doacao;
    }
}

$tiposDoacao = Html::encode(implode(', ', $doacoes));
$textoPrincipal = 'Certificamos que <b>' . Html::encode($nome) . '</b>, participou do Trote Solid&aacute;rio ' . Html::encode($trote)
    . ', na qualidade de ' . Html::encode($qualidade)
    . ', como volunt&aacute;rio(a), realizando doa&ccedil;&otilde;es do tipo: ' . $tiposDoacao
    . ', promovido pelo N&uacute;cleo Acad&ecirc;mico Simers, ' . Html::encode($fraseCertificado);
?>
<style type="text/css">
    .tg { border-collapse: collapse; border-spacing: 0; width: 100%; }
    .tg td, .tg th { font-family: Arial, sans-serif; font-size: 14px; overflow: hidden; padding: 0; word-break: normal; }
    .tg .center { text-align: center; vertical-align: top; }
</style>

<div style="position:relative; width:297mm; height:210mm; overflow:hidden; font-family:Arial, sans-serif; color:#2f1f86;">
    <?php if ($bg !== ''): ?>
        <img src="<?= $bg ?>" alt="Fundo" style="position:absolute; left:0; top:0; width:297mm; height:210mm;" />
    <?php endif; ?>

    <div style="position:absolute; left:13.5mm; top:8mm; width:271.5mm; height:193.5mm; background:#ffffff; border-radius:4mm;"></div>

    <?php if ($elementRight !== ''): ?>
        <img src="<?= $elementRight ?>" alt="Elemento direito" style="position:absolute; right:7.8mm; top:45.5mm; width:20mm;" />
    <?php endif; ?>

    <?php if ($elementLeft !== ''): ?>
        <img src="<?= $elementLeft ?>" alt="Elemento esquerdo" style="position:absolute; left:8.2mm; bottom:17mm; width:17mm;" />
    <?php endif; ?>

    <div style="position:absolute; left:22mm; top:12mm; width:253mm; height:181mm;">
        <table class="tg" style="height:100%; table-layout:fixed;">
            <tbody>
                <tr>
                    <td class="center" style="height:36mm;">
                        <?php if ($logo !== ''): ?>
                            <img src="<?= $logo ?>" alt="Logo Trote Solid&aacute;rio Simers" style="width:57mm;" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="center" style="height:18mm; font-size:15.2mm; color:#2f1f86; font-weight:500;">CERTIFICADO</td>
                </tr>
                <tr>
                    <td class="center" style="padding:7mm 9mm 0; font-size:7.15mm; line-height:1.28; color:#2f1f86;">
                        <?= $textoPrincipal ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top:8mm; vertical-align:bottom;">
                        <table class="tg" style="table-layout:fixed; width:100%;">
                            <tbody>
                                <tr>
                                    <td class="center" style="width:50%;">
                                        <?php if ($assinaturaMarcia !== ''): ?>
                                            <img src="<?= $assinaturaMarcia ?>" alt="Assinatura Marcia" style="width:43mm; display:block; margin:0 auto 1mm;" />
                                        <?php endif; ?>
                                        <div style="font-size:4mm; font-weight:700; color:#23155e;">Dra. Marcia Pires Barbosa</div>
                                        <div style="font-size:3.45mm; margin-top:2mm; color:#23155e;">Diretora de Pol&iacute;ticas Estrat&eacute;gicas</div>
                                    </td>
                                    <td class="center" style="width:50%;">
                                        <?php if ($assinaturaMarcelo !== ''): ?>
                                            <img src="<?= $assinaturaMarcelo ?>" alt="Assinatura Marcelo" style="width:42mm; display:block; margin:0 auto 1mm;" />
                                        <?php endif; ?>
                                        <div style="font-size:4mm; font-weight:700; color:#23155e;">DR. Marcelo Marsillac Matias</div>
                                        <div style="font-size:3.45mm; margin-top:2mm; color:#23155e;">Presidente do Simers</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
