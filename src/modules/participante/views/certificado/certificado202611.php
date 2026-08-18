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
        return $path;
    }

    return Yii::getAlias('@web') . '/img/' . rawurlencode($selectedFileName);
};

$logoPath = $asset('logo-site-2025.png');
$bgPath = $asset('bg-certificado-2025_resized.png');
$elementLeftPath = $asset('element-left.png');
$elementRightPath = $asset('element-right.png');
$assinaturaMarcia = $asset('assinatura-marcia.png');
$assinaturaMarcelo = $asset('assinatura-marcelo.png');

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

<table cellpadding="0" cellspacing="0" style="width:277mm; height:155mm; border-collapse:collapse; font-family:Arial, sans-serif; color:#28148b;">
    <tr>
        <td style="height:155mm; padding:16mm 13mm 0; text-align:center; vertical-align:top; background-color:#520eba; background-image:url('<?= $bgPath ?>'); background-repeat:no-repeat; background-image-resize:6;">
            <img src="<?= $logoPath ?>" alt="Logo Trote Solidário Simers" style="width:52mm; height:auto;">
            <div style="margin-top:3mm; font-size:10mm; line-height:1;">CERTIFICADO</div>
            <div style="margin-top:7mm; font-size:5.8mm; line-height:1.2;"><?= $textoPrincipal ?></div>
            <table cellpadding="0" cellspacing="0" style="width:100%; margin-top:5mm; border-collapse:collapse; color:#2b1d70;">
                <tr>
                    <td style="width:50%; text-align:center; vertical-align:top; font-size:2.7mm; line-height:1.25;">
                        <img src="<?= $assinaturaMarcia ?>" alt="Assinatura de Marcia Pires Barbosa" style="width:48mm; height:auto;">
                    </td>
                    <td style="width:50%; text-align:center; vertical-align:top; font-size:2.7mm; line-height:1.25;">
                        <img src="<?= $assinaturaMarcelo ?>" alt="Assinatura de Marcelo Marsillac Matias" style="width:42mm; height:auto;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
