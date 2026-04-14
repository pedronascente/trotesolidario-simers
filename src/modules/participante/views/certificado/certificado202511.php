<?php

$renderMode = isset($renderMode) && $renderMode === 'pdf' ? 'pdf' : 'web';

$resolveAssetPath = static function (string $fileName): ?string {
    foreach (['@webroot', '@app/web'] as $alias) {
        $basePath = Yii::getAlias($alias, false);
        if (!is_string($basePath) || $basePath === '') {
            continue;
        }

        $candidate = realpath($basePath . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fileName);
        if ($candidate !== false && is_file($candidate)) {
            return $candidate;
        }
    }

    return null;
};

$asset = static function (string $fileName) use ($renderMode, $resolveAssetPath): string {
    $path = $resolveAssetPath($fileName);
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

$logoPath = $asset('logo-site-2025.png');
$iconsPath = $asset('icons.png');
$cornerLeftPath = $asset('top_half.png');
$cornerRightPath = $asset('element-r2-cropped.png');
?>

<div style="width:297mm; height:210mm;margin:0;padding:0;overflow:hidden;background:#ffffff;">
    <table style="width:297mm;height:210mm;border-collapse:collapse;border-spacing:0;table-layout:fixed;margin:0;padding:0;">
        <tr>
            <td style="width:10mm; padding:0; margin:0;"></td>
            <td style="width:277mm;height:210mm;padding:8mm 0 8mm 0;margin:0;vertical-align:top;">
                <table style="width:277mm;height:148mm;border-collapse:collapse;border-spacing:0;table-layout:fixed;margin:0;padding:0;background:#5c1fd0;">
                    <!-- topo -->
                    <!-- logo + ornamento superior direito -->
                    <tr>
                        <td style="width:18mm; height:33mm; padding:0; margin:0;"></td>
                        <td style="width:229mm;height:33mm;padding-top:20mm;margin:0;text-align:center;vertical-align:top;">
                            <?php if ($logoPath !== ''): ?>
                                <img src="<?= $logoPath ?>" alt="Logo Trote Solidário Simers" style="display:block;width:50mm;height:auto;margin:0 auto;border:0;outline:none;"/>
                            <?php endif; ?>
                        </td>
                        <!-- imagem superior direito -->        
                        <td style="width:24mm;height:33mm;padding:0;margin:0;vertical-align:top;text-align:right;">
                            <?php if ($cornerRightPath !== ''): ?>
                                <img src="<?= $cornerRightPath ?>" alt="" style="display:block;width:24mm;height:auto;margin:0 0 0 auto;border:0;outline:none;"/>                            
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- espaço -->
                    <tr>
                        <td colspan="3" style="height:9mm; padding:0; margin:0;"></td>
                    </tr>
                    <!-- texto -->
                    <tr>
                        <td style="width:18mm; padding:0; margin:0;"></td>
                        <td colspan="2" style="width:259mm; padding:0 14mm 0 0;margin:0; vertical-align:top;">
                            <table style=" width:100%;border-collapse:collapse;border-spacing:0;table-layout:fixed;margin:0;padding:0;">
                                <tr>
                                   <td style="width:4mm; padding:0; margin:0;"></td>
                                   <td style="padding:0; margin:0; font-family:Arial, Helvetica, sans-serif; font-size:5.00mm; line-height:1.2; color:#ffffff; text-align:justify;text-justify:inter-word;vertical-align:top;font-weight:normal;">
                                        O Trote Solidário é um projeto realizado pelo Núcleo Acadêmico Simers desde 2008. Esta ação é a união da campanha de doação de sangue realizada pelos ingressantes das 
                                        universidades de medicina e o convite à sociedade para doar alimentos às comunidades carentes, que mantém a tradição do trote universitário, tornando realidade o objetivo 
                                        de todos os médicos: SALVAR VIDAS. Como reconhecimento público, o Trote Solidário foi vencedor do Prêmio Top Cidadania 2013 da ABRH-RS e do Prêmio Ser Humano Oswaldo 
                                        Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustentável e Responsabilidade Social/ Organização Cidadã, Diploma de Honra ao Mérito da Câmara Municipal
                                        de Porto Alegre no ano de 2022 e prêmio Top Cidadania da ABRH-RS, na categoria organização em 2022 e 2024. Atualmente o Trote Solidário promove as seguintes ações: cadastro 
                                        de medula óssea, doação de alimentos, sangue e tampinhas plásticas.
                                   </td>
                                   <td style="width:10mm; padding:0; margin:0;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- espaço -->
                    <tr>
                        <td colspan="3" style="height:8mm; padding:0; margin:0;"></td>
                    </tr>
                    <!-- ícones -->
                    <tr>
                        <td colspan="3" style="height:15mm;padding:0;margin:0;text-align:center;vertical-align:top;">
                            <?php if ($iconsPath !== ''): ?>
                                <img src="<?= $iconsPath ?>" alt="Ícones institucionais" style=" display:block;width:70mm; height:auto;margin:0 auto; border:0;outline:none;"/>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <!-- respiro -->
                    <tr>
                        <td colspan="3" style="height:18mm; padding:0; margin:0;"></td>
                    </tr>
                    <!-- ornamento inferior esquerdo -->
                    <tr>
                        <td colspan="3" style="height:20mm;padding:0;margin:0;vertical-align:bottom;">
                            <table style="width:277mm;height:20mm;border-collapse:collapse;border-spacing:0;table-layout:fixed;margin:0;padding:0;">
                                <tr>
                                    <td style=" width:64mm; height:20mm; padding:0; margin:0; vertical-align:bottom;text-align:left;">
                                        <?php if ($cornerLeftPath !== ''): ?>
                                            <div style="width:64mm; height:20mm; overflow:hidden;margin:0; padding:0;">
                                                <img src="<?= $cornerLeftPath ?>" alt="" style=" display:block;width:76mm;height:auto;margin:0;border:0;outline:none;"/>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="width:213mm; height:20mm; padding:0; margin:0;"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width:10mm; padding:0; margin:0;"></td>
        </tr>
    </table>
</div>