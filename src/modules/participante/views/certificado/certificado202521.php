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
        return $path;
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

$textoPrincipal = 'Certificamos que <b>' . Html::encode($nome) . '</b>, participou do Trote Solidário ' . Html::encode($trote) . ', na qualidade de ' . Html::encode($qualidade)
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

$textoPrincipal .= '.';
$backgroundPath = $asset('bg-certificado-2025_resized.png');
$logoPath = $asset('logo-site.png');
$assinaturaMarciaPath = $asset('assinatura-marcia.png');
$assinaturaMarceloPath = $asset('assinatura-marcelo.png');
?>
<table cellpadding="0" cellspacing="0" style="width:277mm; height:155mm; border-collapse:collapse; font-family:Arial, sans-serif; color:#2e056b;">
    <tr>
        <td style="height:155mm; padding:0 13mm; text-align:center; vertical-align:top; background-color:#520eba; background-image:url('<?= $backgroundPath ?>'); background-repeat:no-repeat; background-image-resize:6;">
            <div style="height:12mm;"></div>
            <img src="<?= $logoPath ?>" alt="Logo Trote Solidário Simers" style="width:52mm; height:auto;">
            <div style="margin-top:3mm; font-size:10mm; line-height:1;">CERTIFICADO</div>
            <div style="margin-top:7mm; font-size:5.8mm; line-height:1.2;"><?= $textoPrincipal ?></div>
            <table cellpadding="0" cellspacing="0" style="width:100%; margin-top:5mm; border-collapse:collapse; color:#2e056b;">
                <tr>
                    <td style="width:50%; text-align:center; vertical-align:top; font-size:2.7mm; line-height:1.25;">
                        <img src="<?= $assinaturaMarciaPath ?>" alt="Assinatura de Marcia Pires Barbosa" style="width:48mm; height:auto;">
                    </td>
                    <td style="width:50%; text-align:center; vertical-align:top; font-size:2.7mm; line-height:1.25;">
                        <img src="<?= $assinaturaMarceloPath ?>" alt="Assinatura de Marcelo Marsillac Matias" style="width:42mm; height:auto;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
