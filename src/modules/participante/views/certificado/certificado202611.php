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
    . ', como voluntário(a), realizando doações do tipo: ' . Html::encode($tiposDoacao)
    . ', promovido pelo Núcleo Acadêmico Simers, '
    . Html::encode($fraseBase) . ' ' . $totalHoras . ' horas.';
?>

<div style="
    position:relative;
    width:297mm;
    height:210mm;
    overflow:hidden;
    font-family:Arial, Helvetica, sans-serif;
    color:#28148b;
">

    <?php if ($bgPath !== ''): ?>
        <img
            src="<?= $bgPath ?>"
            alt=""
            style="
                position:absolute;
                left:0;
                top:0;
                width:297mm;
                height:210mm;
            ">
    <?php endif; ?>

    <!-- Moldura externa branca -->
    <div style="
        position:absolute;
        left:8.5mm;
        top:6.5mm;
        width:280mm;
        height:191mm;
        background:#ffffff;
        border-radius:6mm;
        z-index:1;
    "></div>

    <!-- Moldura interna clara -->
    <div style="
        position:absolute;
        left:11mm;
        top:9mm;
        width:274.5mm;
        height:185.5mm;
        background:#f5f5f5;
        border-radius:4.2mm;
        z-index:2;
    "></div>

    <?php if ($elementRightPath !== ''): ?>
        <img
            src="<?= $elementRightPath ?>"
            alt=""
            style="
                position:absolute;
                right:10mm;
                top:46mm;
                width:18mm;
                z-index:3;
            ">
    <?php endif; ?>

    <?php if ($elementLeftPath !== ''): ?>
        <img
            src="<?= $elementLeftPath ?>"
            alt=""
            style="
                position:absolute;
                left:8.5mm;
                bottom:18mm;
                width:17.5mm;
                z-index:3;
            ">
    <?php endif; ?>

    <div style="
        position:absolute;
        left:0;
        top:10.5mm;
        width:297mm;
        text-align:center;
        z-index:4;
    ">
        <?php if ($logoPath !== ''): ?>
            <img
                src="<?= $logoPath ?>"
                alt=""
                style="
                    width:52mm;
                    height:auto;
                ">
        <?php endif; ?>
    </div>

    <div style="
        position:absolute;
        left:0;
        top:55mm;
        width:297mm;
        text-align:center;
        font-size:12.8mm;
        font-weight:normal;
        letter-spacing:0.15mm;
        color:#28148b;
        z-index:4;
    ">
        CERTIFICADO
    </div>

    <div style="
        position:absolute;
        left:31mm;
        top:81mm;
        width:235mm;
        text-align:center;
        font-size:7.15mm;
        line-height:1.24;
        color:#28148b;
        z-index:4;
    ">
        <?= $textoPrincipal ?>
    </div>

    <div style="
        position:absolute;
        left:40mm;
        top:136mm;
        width:64mm;
        text-align:center;
        color:#2b1d70;
        z-index:4;
    ">
        <?php if ($assinaturaMarcia !== ''): ?>
            <img
                src="<?= $assinaturaMarcia ?>"
                alt=""
                style="
                    width:46mm;
                    height:auto;
                ">
        <?php endif; ?>

        <div style="
            margin:0.8mm auto 1.8mm auto;
            width:54mm;
            border-top:0.2mm solid #b8b8b8;
            height:0;
        "></div>

        <div style="
            font-size:3.9mm;
            font-weight:bold;
            line-height:1.1;
        ">
            Dra. Marcia Pires Barbosa
        </div>

        <div style="
            font-size:3.35mm;
            margin-top:2mm;
            line-height:1.1;
        ">
            Diretora de Políticas Estratégicas
        </div>
    </div>

    <div style="
        position:absolute;
        right:40mm;
        top:136mm;
        width:64mm;
        text-align:center;
        color:#2b1d70;
        z-index:4;
    ">
        <?php if ($assinaturaMarcelo !== ''): ?>
            <img
                src="<?= $assinaturaMarcelo ?>"
                alt=""
                style="
                    width:42mm;
                    height:auto;
                ">
        <?php endif; ?>

        <div style="
            margin:0.8mm auto 1.8mm auto;
            width:42mm;
            border-top:0.2mm solid #b8b8b8;
            height:0;
        "></div>

        <div style="
            font-size:3.9mm;
            font-weight:bold;
            line-height:1.1;
            text-transform:uppercase;
        ">
            Dr. Marcelo Marsillac Matias
        </div>

        <div style="
            font-size:3.35mm;
            margin-top:2mm;
            line-height:1.1;
        ">
            Presidente do Simers
        </div>
    </div>
</div>