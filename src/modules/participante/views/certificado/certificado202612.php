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
        return $path;
    }

    return Yii::getAlias('@web') . '/img/' . rawurlencode($selectedFileName);
};

$logoPath = $asset('logo-site-2025.png');
$bgPath = $asset('bg-certificado-2025-2_resized.png');
$iconsPath = $asset('icons.png');

$textoProjeto = 'O Trote Solid&aacute;rio &eacute; um projeto realizado pelo N&uacute;cleo Acad&ecirc;mico Simers desde 2008. Esta a&ccedil;&atilde;o &eacute; a uni&atilde;o da campanha de doa&ccedil;&atilde;o de sangue realizada pelos ingressantes das universidades de medicina e o convite &agrave; sociedade para doar alimentos &agrave;s comunidades carentes, que mant&eacute;m a tradi&ccedil;&atilde;o do trote universit&aacute;rio, tornando realidade o objetivo de todos os m&eacute;dicos: SALVAR VIDAS. Como reconhecimento p&uacute;blico, o Trote Solid&aacute;rio foi vencedor do Pr&ecirc;mio Top Cidadania 2013 da ABRH-RS e do Pr&ecirc;mio Ser Humano Oswaldo Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustent&aacute;vel e Responsabilidade Social/ Organiza&ccedil;&atilde;o Cidad&atilde;, Diploma de Honra ao M&eacute;rito da C&acirc;mara Municipal de Porto Alegre no ano de 2022 e pr&ecirc;mio Top Cidadania da ABRH-RS, na categoria organiza&ccedil;&atilde;o em 2022 e 2024. Atualmente o Trote Solid&aacute;rio promove as seguintes a&ccedil;&otilde;es: cadastro de medula &oacute;ssea, doa&ccedil;&atilde;o de alimentos, sangue e tampinhas pl&aacute;sticas.';
?>
<table cellpadding="0" cellspacing="0" style="width:277mm; height:155mm; border-collapse:collapse; font-family:Arial, sans-serif; color:#ffffff;">
    <tr>
        <td style="height:155mm; padding:8mm 14mm 0; text-align:center; vertical-align:top; background-color:#520eba; background-image:url('<?= $bgPath ?>'); background-repeat:no-repeat; background-image-resize:6;">
            <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse; color:#ffffff;">
                <tr>
                    <td style="text-align:center;"><img src="<?= $logoPath ?>" alt="Logo Trote Solid&aacute;rio Simers" style="width:52mm; height:auto;"></td>
                </tr>
                <tr><td style="height:7mm; font-size:0; line-height:0;">&nbsp;</td></tr>
                <tr>
                    <td style="font-size:4.8mm; line-height:1.22; text-align:justify;"><?= $textoProjeto ?></td>
                </tr>
                <tr>
                    <td style="padding-top:7mm; text-align:center;"><img src="<?= $iconsPath ?>" alt="&Iacute;cones institucionais do projeto" style="width:53mm; height:auto;"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
