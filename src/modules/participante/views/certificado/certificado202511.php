<?php 

use app\modules\participante\models\Helper;
use yii\helpers\Url;

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
        return $path;
    }

    return Yii::getAlias('@web') . '/img/' . rawurlencode($fileName);
};

$bGcertificado20252Resized = $asset('bg-certificado-2025-2_resized.png');
$logoSite2025 = $asset('logo-site-2025.png');
$icons = $asset('icons.png');
?>
<table cellpadding="0" cellspacing="0" style="width:277mm; height:155mm; border-collapse:collapse; font-family:Arial, sans-serif; color:#ffffff;">
    <tr>
        <td style="height:155mm; padding:8mm 14mm 0; text-align:center; vertical-align:top; background-color:#520eba; background-image:url('<?= $bGcertificado20252Resized ?>'); background-repeat:no-repeat; background-image-resize:6;">
            <table cellpadding="0" cellspacing="0" style="width:100%; border-collapse:collapse; color:#ffffff;">
                <tr>
                    <td style="text-align:center;"><img src="<?= $logoSite2025 ?>" alt="Logo Trote Solidário Simers" style="width:52mm; height:auto;"></td>
                </tr>
                <tr><td style="height:7mm; font-size:0; line-height:0;">&nbsp;</td></tr>
                <tr>
                    <td style="text-align:justify; font-size:4.9mm; line-height:1.22;">O Trote Solidário é um projeto realizado pelo Núcleo Acadêmico Simers desde 2008. Esta ação é a união da campanha de doação de sangue realizada pelos ingressantes das universidades de medicina e o convite à sociedade para doar alimentos às comunidades carentes, que mantém a tradição do trote universitário, tornando realidade o objetivo de todos os médicos: SALVAR VIDAS. Como reconhecimento público, o Trote Solidário foi vencedor do Prêmio Top Cidadania 2013 da ABRH-RS e do Prêmio Ser Humano Oswaldo Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustentável e Responsabilidade Social/ Organização Cidadã, Diploma de Honra ao Mérito da Câmara Municipal de Porto Alegre no ano de 2022 e prêmio Top Cidadania da ABRH-RS, na categoria organização em 2022 e 2024. Atualmente o Trote Solidário promove as seguintes ações: cadastro de medula óssea, doação de alimentos, sangue e tampinhas plásticas.</td>
                </tr>
                <tr>
                    <td style="padding-top:7mm; text-align:center;"><img src="<?= $icons ?>" alt="Ícones institucionais" style="width:53mm; height:auto;"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
