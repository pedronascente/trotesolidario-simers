<?php

$renderMode = isset($renderMode) && $renderMode === 'pdf' ? 'pdf' : 'web';

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
$bg = $asset('bg-certificado-2025-2_resized.png');
$icons = $asset('icons.png');

$textoProjeto = 'O Trote Solid&aacute;rio &eacute; um projeto realizado pelo N&uacute;cleo Acad&ecirc;mico Simers desde 2008. Esta a&ccedil;&atilde;o &eacute; a uni&atilde;o da campanha de doa&ccedil;&atilde;o de sangue realizada pelos ingressantes das universidades de medicina e o convite &agrave; sociedade para doar alimentos &agrave;s comunidades carentes, que mant&ecirc;m a tradi&ccedil;&atilde;o do trote universit&aacute;rio, tornando realidade o objetivo de todos os m&eacute;dicos: SALVAR VIDAS. Como reconhecimento p&uacute;blico, o Trote Solid&aacute;rio foi vencedor do Pr&ecirc;mio Top Cidadania 2013 da ABRH-RS e do Pr&ecirc;mio Ser Humano Oswaldo Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustent&aacute;vel e Responsabilidade Social/ Organiza&ccedil;&atilde;o Cidad&atilde;, Diploma de Honra ao M&eacute;rito da C&acirc;mara Municipal de Porto Alegre no ano de 2022 e pr&ecirc;mio Top Cidadania da ABRH-RS, na categoria organiza&ccedil;&atilde;o em 2022 e 2024. Atualmente o Trote Solid&aacute;rio promove as seguintes a&ccedil;&otilde;es: cadastro de medula &oacute;ssea, doa&ccedil;&atilde;o de alimentos, sangue e tampinhas pl&aacute;sticas.';
?>
<style type="text/css">
    .tg { border-collapse: collapse; border-spacing: 0; width: 100%; }
    .tg td, .tg th { font-family: Arial, sans-serif; font-size: 14px; overflow: hidden; padding: 0; word-break: normal; }
    .tg .center { text-align: center; vertical-align: top; }
</style>

<div style="position:relative; width:297mm; height:210mm; overflow:hidden; font-family:Arial, sans-serif; color:#ffffff;">
    <?php if ($bg !== ''): ?>
        <img src="<?= $bg ?>" alt="Fundo" style="position:absolute; left:0; top:0; width:297mm; height:210mm;" />
    <?php endif; ?>

    <div style="position:absolute; left:15mm; top:8mm; width:267mm; height:191mm;">
        <table class="tg" style="height:100%; table-layout:fixed;">
            <tbody>
                <tr>
                    <td class="center" style="height:38mm;">
                        <?php if ($logo !== ''): ?>
                            <img src="<?= $logo ?>" alt="Logo Trote Solid&aacute;rio Simers" style="width:56mm;" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding:8mm 14mm 0; font-size:6.1mm; line-height:1.28; color:#ffffff; text-align:left;">
                        <?= $textoProjeto ?>
                    </td>
                </tr>
                <tr>
                    <td class="center" style="padding-top:9mm; vertical-align:bottom;">
                        <?php if ($icons !== ''): ?>
                            <img src="<?= $icons ?>" alt="&Iacute;cones do projeto" style="width:44mm;" />
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
