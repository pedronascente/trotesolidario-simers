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

$textoPrincipal .= '.';

$backgroundPath = $asset('bg-certificado-2025_resized.png');
$logoPath = $asset('logo-site-2025.png');
$assinaturaMarciaPath = $asset('dramarcia.png');
$assinaturaMarceloPath = $asset('drmarcelomarsillac.png');
?>

<div style="width:297mm; height:210mm; margin:0; padding:0; overflow:hidden; background:#5b19c8;">
    <table style="
        width:297mm;
        height:210mm;
        border-collapse:collapse;
        border-spacing:0;
        table-layout:fixed;
        margin:0;
        padding:0;
        background-color:#5b19c8;
        <?php if ($backgroundPath !== ''): ?>
            background-image:url('<?= $backgroundPath ?>');
            background-repeat:no-repeat;
            background-position:center center;
            background-size:297mm 210mm;
        <?php endif; ?>
    ">
        <tr>
            <td style="padding:18mm 16mm 16mm 16mm; vertical-align:top;">
                <table style="width:100%; height:174mm; border-collapse:collapse; border-spacing:0; table-layout:fixed;">
                    <tr>
                        <td style="height:31mm; text-align:center; vertical-align:top; padding:0;">
                            <?php if ($logoPath !== ''): ?>
                                <img src="<?= $logoPath ?>" alt="Logo Trote Solidário Simers" style="width:53mm; height:auto; display:block; margin:0 auto;">
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:19mm; text-align:center; vertical-align:middle; font-family:Arial, Helvetica, sans-serif; font-size:12.2mm; line-height:1; font-weight:400; color:#4b27b4; padding:0;">
                            CERTIFICADO
                        </td>
                    </tr>
                    <tr>
                        <td style="height:63mm; padding:8mm 15mm 0 15mm; text-align:center; vertical-align:top; font-family:Arial, Helvetica, sans-serif; font-size:6.45mm; line-height:1.24; color:#2f1ca0;">
                            <?= $textoPrincipal ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:18mm;"></td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top; padding:0 18mm;">
                            <table style="width:100%; border-collapse:collapse; border-spacing:0; table-layout:fixed;">
                                <tr>
                                    <td style="width:50%; text-align:center; vertical-align:top; color:#2b1d70; padding-left:8mm;">
                                        <?php if ($assinaturaMarciaPath !== ''): ?>
                                            <img src="<?= $assinaturaMarciaPath ?>" alt="Dra. Marcia Pires Barbosa" style="width:65mm; height:auto; display:block; margin:0 auto;">
                                        <?php endif; ?>
                                        <div style="font-family:Arial, Helvetica, sans-serif; font-size:2.55mm; font-weight:700; line-height:1.1; margin-top:0.6mm;">
                                            <b>DRA. MARCIA PIRES BARBOSA</b>
                                        </div>
                                        <div style="font-family:Arial, Helvetica, sans-serif; font-size:3.05mm; line-height:1.1; margin-top:1.6mm;">
                                            Diretora de Políticas Estratégicas
                                        </div>
                                    </td>
                                    <td style="width:50%; text-align:center; vertical-align:top; color:#2b1d70; padding-left:8mm;">
                                        <?php if ($assinaturaMarceloPath !== ''): ?>
                                            <img src="<?= $assinaturaMarceloPath ?>" alt="Assinatura Dr. Marcelo Marsillac Matias" style="width:42mm; height:auto; display:block; margin:0 auto;">
                                        <?php endif; ?>
                                        <div style="font-family:Arial, Helvetica, sans-serif; font-size:2.55mm; font-weight:700; line-height:1.1; margin-top:0.6mm;">
                                            <b>DR. Marcelo Marsillac Matias</b>
                                        </div>
                                        <div style="font-family:Arial, Helvetica, sans-serif; font-size:3.05mm; line-height:1.1; margin-top:1.6mm;">
                                            Presidente do Simers
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
