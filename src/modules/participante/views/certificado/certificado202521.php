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

$asset = static function (string $pdfFileName, ?string $webFileName = null) use ($renderMode): string {
    $webFileName = $webFileName ?? $pdfFileName;
    $selectedFileName = $renderMode === 'pdf' ? $pdfFileName : $webFileName;
    $path = null;

    foreach (['@webroot', '@app/web'] as $alias) {
        $basePath = Yii::getAlias($alias, false);
        if (!is_string($basePath) || $basePath === '') {
            continue;
        }

        $candidate = realpath($basePath . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $selectedFileName);
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

    return Yii::getAlias('@web') . '/img/' . rawurlencode($selectedFileName);
};

$logoPath = $asset('logo-site-2025.jpg', 'logo-site-2025.png');
$bgPath = $asset('bg-certificado-2025_resized.jpg', 'bg-certificado-2025_resized.png');
$elementLeftPath = $asset('element-left.jpg', 'element-left.png');
$elementRightPath = $asset('element-right.jpg', 'element-right.png');
$assinaturaMarcia = $asset('assinatura-marcia.jpg', 'assinatura-marcia.png');
$assinaturaMarcelo = $asset('assinatura-marcelo.jpg', 'assinatura-marcelo.png');

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

$tiposDoacao = implode(', ', $doacoes);
$textoPrincipal = 'Certificamos que <b>' . Html::encode($nome) . '</b>, participou do Trote Solid&aacute;rio ' . Html::encode($trote)
    . ', na qualidade de ' . Html::encode($qualidade)
    . ', como volunt&aacute;rio(a), realizando doa&ccedil;&otilde;es do tipo: ' . Html::encode($tiposDoacao)
    . ', promovido pelo N&uacute;cleo Acad&ecirc;mico Simers, ' . Html::encode($fraseCertificado);
?>
<div style="position:relative; width:297mm; height:210mm; overflow:hidden; font-family:Arial, sans-serif; color:#5a2ca0;">
    <?php if ($bgPath !== ''): ?>
        <img src="<?= $bgPath ?>" alt="Fundo do certificado" style="position:absolute; left:0; top:0; width:297mm; height:210mm;">
    <?php endif; ?>

    <div style="position:absolute; left:13.5mm; top:8.2mm; width:271.5mm; height:193mm; background:#ffffff; border-radius:4mm;"></div>

    <?php if ($elementRightPath !== ''): ?>
        <img src="<?= $elementRightPath ?>" alt="Elemento decorativo superior direito" style="position:absolute; right:7.5mm; top:46.5mm; width:20mm;">
    <?php endif; ?>

    <?php if ($elementLeftPath !== ''): ?>
        <img src="<?= $elementLeftPath ?>" alt="Elemento decorativo inferior esquerdo" style="position:absolute; left:8mm; bottom:18mm; width:17mm;">
    <?php endif; ?>

    <div style="position:absolute; left:22mm; top:11mm; width:253mm; height:184mm; text-align:center;">
        <?php if ($logoPath !== ''): ?>
            <img src="<?= $logoPath ?>" alt="Logo Trote Solid&aacute;rio Simers" style="display:block; width:57mm; margin:0 auto; margin-top:4mm;">
        <?php endif; ?>

        <div style="margin-top:5mm; font-size:15.5mm; line-height:1; font-weight:500; letter-spacing:0; color:#4b1fa8;">CERTIFICADO</div>

        <div style="margin:11mm auto 0; width:236mm; font-size:7.05mm; line-height:1.28; color:#4b1fa8; text-align:center;">
            <?= $textoPrincipal ?>
        </div>

        <div style="position:absolute; left:18mm; right:18mm; bottom:22mm; height:32mm; color:#35206d;">
            <div style="position:absolute; left:25mm; width:75mm; text-align:center;">
                <?php if ($assinaturaMarcia !== ''): ?>
                    <img src="<?= $assinaturaMarcia ?>" alt="Assinatura Dra. Marcia Pires Barbosa" style="display:block; width:44mm; margin:0 auto 1mm;">
                <?php endif; ?>
                <div style="font-size:4.05mm; font-weight:700;">Dra. Marcia Pires Barbosa</div>
                <div style="font-size:3.45mm; margin-top:2.2mm;">Diretora de Pol&iacute;ticas Estrat&eacute;gicas</div>
            </div>

            <div style="position:absolute; right:21mm; width:75mm; text-align:center;">
                <?php if ($assinaturaMarcelo !== ''): ?>
                    <img src="<?= $assinaturaMarcelo ?>" alt="Assinatura Dr. Marcelo Marsillac Matias" style="display:block; width:42mm; margin:0 auto 1mm;">
                <?php endif; ?>
                <div style="font-size:4.05mm; font-weight:700;">DR. Marcelo Marsillac Matias</div>
                <div style="font-size:3.45mm; margin-top:2.2mm;">Presidente do Simers</div>
            </div>
        </div>
    </div>
</div>
