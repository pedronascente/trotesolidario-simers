<?php

$logoPath = realpath(Yii::getAlias('@webroot') . '/img/logo-site-2025.png') ?: realpath(Yii::getAlias('@webroot') . '/img/logo-site.png');
$bgPath = realpath(Yii::getAlias('@webroot') . '/img/bg-certificado-2025-2_resized.png');
$iconsPath = realpath(Yii::getAlias('@webroot') . '/img/icons.png');

$textoProjeto = "O Trote Solid\u{00E1}rio \u{00E9} um projeto realizado pelo N\u{00FA}cleo Acad\u{00EA}mico Simers desde 2008. Esta a\u{00E7}\u{00E3}o \u{00E9} a uni\u{00E3}o da campanha de doa\u{00E7}\u{00E3}o de sangue realizada pelos ingressantes das universidades de medicina e o convite \u{00E0} sociedade para doar alimentos \u{00E0}s comunidades carentes, que mant\u{00EA}m a tradi\u{00E7}\u{00E3}o do trote universit\u{00E1}rio, tornando realidade o objetivo de todos os m\u{00E9}dicos: SALVAR VIDAS. Como reconhecimento p\u{00FA}blico, o Trote Solid\u{00E1}rio foi vencedor do Pr\u{00EA}mio Top Cidadania 2013 da ABRH-RS e do Pr\u{00EA}mio Ser Humano Oswaldo Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustent\u{00E1}vel e Responsabilidade Social/ Organiza\u{00E7}\u{00E3}o Cidad\u{00E3}, Diploma de Honra ao M\u{00E9}rito da C\u{00E2}mara Municipal de Porto Alegre no ano de 2022 e pr\u{00EA}mio Top Cidadania da ABRH-RS, na categoria organiza\u{00E7}\u{00E3}o em 2022 e 2024. Atualmente o Trote Solid\u{00E1}rio promove as seguintes a\u{00E7}\u{00F5}es: cadastro de medula \u{00F3}ssea, doa\u{00E7}\u{00E3}o de alimentos, sangue e tampinhas pl\u{00E1}sticas.";
?>
<div style="position:relative; width:297mm; height:210mm; overflow:hidden; font-family:Arial, sans-serif; color:#ffffff;">
    <?php if ($bgPath): ?>
        <img src="<?= $bgPath ?>" alt="Fundo do certificado" style="position:absolute; left:0; top:0; width:297mm; height:210mm;">
    <?php endif; ?>

    <div style="position:absolute; left:15mm; top:8mm; width:267mm; height:191mm; text-align:center;">
        <?php if ($logoPath): ?>
            <img src="<?= $logoPath ?>" alt="Logo Trote Solid\u{00E1}rio Simers" style="display:block; width:56mm; margin:0 auto; margin-top:1mm;">
        <?php endif; ?>

        <div style="margin:8mm auto 0; width:252mm; font-size:6.05mm; line-height:1.28; text-align:left; color:#ffffff;">
            <?= $textoProjeto ?>
        </div>

        <?php if ($iconsPath): ?>
            <img src="<?= $iconsPath ?>" alt="\u{00CD}cones do projeto" style="display:block; width:44mm; margin:10.5mm auto 0;">
        <?php endif; ?>
    </div>
</div>
