<?php

$renderMode = isset($renderMode) && $renderMode === 'pdf' ? 'pdf' : 'web';

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
$bgPath = $asset('bg-certificado-2025-2_resized.jpg', 'bg-certificado-2025-2_resized.png');
$iconsPath = $asset('icons.jpg', 'icons.png');

$textoProjeto = 'O Trote Solid&aacute;rio ? um projeto realizado pelo N&uacute;cleo Acad&ecirc;mico Simers desde 2008. Esta a?o ? a uni&atilde;o da campanha de doa&ccedil;&atilde;o de sangue realizada pelos ingressantes das universidades de medicina e o convite ? sociedade para doar alimentos &agrave;s comunidades carentes, que mant&eacute;m a tradi&ccedil;&atilde;o do trote universit&aacute;rio, tornando realidade o objetivo de todos os m&eacute;dicos: SALVAR VIDAS. Como reconhecimento p&uacute;blico, o Trote Solid&aacute;rio foi vencedor do Pr&ecirc;mio Top Cidadania 2013 da ABRH-RS e do Pr&ecirc;mio Ser Humano Oswaldo Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustent&aacute;vel e Responsabilidade Social/ Organiza&ccedil;&atilde;o Cidad&atilde;, Diploma de Honra ao M?rito da C?mara Municipal de Porto Alegre no ano de 2022 e pr&ecirc;mio Top Cidadania da ABRH-RS, na categoria organiza&ccedil;&atilde;o em 2022 e 2024. Atualmente o Trote Solid&aacute;rio promove as seguintes a&ccedil;&otilde;es: cadastro de medula &oacute;ssea, doa&ccedil;&atilde;o de alimentos, sangue e tampinhas pl?sticas.';
?>
<div style="position:relative; width:297mm; height:210mm; overflow:hidden; font-family:Arial, sans-serif; color:#ffffff;">
    <?php if ($bgPath !== ''): ?>
        <img src="<?= $bgPath ?>" alt="Fundo institucional do certificado" style="position:absolute; left:0; top:0; width:297mm; height:210mm;">
    <?php endif; ?>

    <div style="position:absolute; left:15mm; top:8mm; width:267mm; height:191mm; text-align:center;">
        <?php if ($logoPath !== ''): ?>
            <img src="<?= $logoPath ?>" alt="Logo Trote Solid&aacute;rio Simers" style="display:block; width:56mm; margin:0 auto; margin-top:1mm;">
        <?php endif; ?>

        <div style="margin:8mm auto 0; width:252mm; font-size:6.05mm; line-height:1.28; text-align:left; color:#ffffff;">
            <?= $textoProjeto ?>
        </div>

        <?php if ($iconsPath !== ''): ?>
            <img src="<?= $iconsPath ?>" alt="&Iacute;cones institucionais do projeto" style="display:block; width:44mm; margin:10.5mm auto 0;">
        <?php endif; ?>
    </div>
</div>
