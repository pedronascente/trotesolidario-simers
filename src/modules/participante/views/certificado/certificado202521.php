<?php

use yii\helpers\Html;

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

$logoPath = realpath(Yii::getAlias('@webroot') . '/img/logo-site-2025.png') ?: realpath(Yii::getAlias('@webroot') . '/img/logo-site.png');
$bgPath = realpath(Yii::getAlias('@webroot') . '/img/bg-certificado-2025_resized.png');
$elementLeftPath = realpath(Yii::getAlias('@webroot') . '/img/element-left.png');
$elementRightPath = realpath(Yii::getAlias('@webroot') . '/img/element-right.png');
$assinaturaMarcia = realpath(Yii::getAlias('@webroot') . '/img/assinatura-marcia.png');
$assinaturaMarcelo = realpath(Yii::getAlias('@webroot') . '/img/assinatura-marcelo.png');

$nome = $normalize($model['name'] ?? '-');
$trote = $normalize($model['trote'] ?? '-');
$qualidade = mb_strtoupper($normalize($model['qualidade'] ?? 'PARTICIPANTE'), 'UTF-8');
$totalHoras = (int) ($model['total_horas'] ?? 0);
$fraseBase = $normalize($model['frase_certificado'] ?? '');
$fraseCertificado = trim($fraseBase . ' ' . $totalHoras . ' horas.');

$doacoes = [];
foreach (($model['all_donations'] ?? []) as $doacao) {
    $doacao = $normalize($doacao);
    if ($doacao !== '') {
        $doacoes[] = $doacao;
    }
}

$tiposDoacao = implode(', ', $doacoes);
$textoPrincipal = "Certificamos que <b>" . Html::encode($nome) . "</b>, participou do Trote Solid\u{00E1}rio " . Html::encode($trote)
    . ", na qualidade de " . Html::encode($qualidade)
    . ", como volunt\u{00E1}rio(a), realizando doa\u{00E7}\u{00F5}es do tipo: " . Html::encode($tiposDoacao)
    . ", promovido pelo N\u{00FA}cleo Acad\u{00EA}mico Simers, " . Html::encode($fraseCertificado);
?>
<div style="position:relative; width:297mm; height:210mm; overflow:hidden; font-family:Arial, sans-serif; color:#2a1974;">
    <?php if ($bgPath): ?>
        <img src="<?= $bgPath ?>" alt="Fundo do certificado" style="position:absolute; left:0; top:0; width:297mm; height:210mm;">
    <?php endif; ?>

    <div style="position:absolute; left:13.5mm; top:8.2mm; width:271.5mm; height:193mm; background:#ffffff; border-radius:4mm;"></div>

    <?php if ($elementRightPath): ?>
        <img src="<?= $elementRightPath ?>" alt="Elemento decorativo" style="position:absolute; right:7.5mm; top:46.5mm; width:20mm;">
    <?php endif; ?>

    <?php if ($elementLeftPath): ?>
        <img src="<?= $elementLeftPath ?>" alt="Elemento decorativo" style="position:absolute; left:8mm; bottom:18mm; width:17mm;">
    <?php endif; ?>

    <div style="position:absolute; left:22mm; top:11mm; width:253mm; height:184mm; text-align:center;">
        <?php if ($logoPath): ?>
            <img src="<?= $logoPath ?>" alt="Logo Trote Solid\u{00E1}rio Simers" style="display:block; width:57mm; margin:0 auto; margin-top:4mm;">
        <?php endif; ?>

        <div style="margin-top:5mm; font-size:15.5mm; line-height:1; font-weight:500; letter-spacing:0; color:#2f1f86;">CERTIFICADO</div>

        <div style="margin:11mm auto 0; width:236mm; font-size:7.05mm; line-height:1.28; color:#2f1f86; text-align:center;">
            <?= $textoPrincipal ?>
        </div>

        <div style="position:absolute; left:18mm; right:18mm; bottom:22mm; height:32mm; color:#23155e;">
            <div style="position:absolute; left:25mm; width:75mm; text-align:center;">
                <?php if ($assinaturaMarcia): ?>
                    <img src="<?= $assinaturaMarcia ?>" alt="Assinatura Dra. Marcia Pires Barbosa" style="display:block; width:44mm; margin:0 auto 1mm;">
                <?php endif; ?>
                <div style="font-size:4.05mm; font-weight:700;">Dra. Marcia Pires Barbosa</div>
                <div style="font-size:3.45mm; margin-top:2.2mm;">Diretora de Pol\u{00ED}ticas Estrat\u{00E9}gicas</div>
            </div>

            <div style="position:absolute; right:21mm; width:75mm; text-align:center;">
                <?php if ($assinaturaMarcelo): ?>
                    <img src="<?= $assinaturaMarcelo ?>" alt="Assinatura Dr. Marcelo Marsillac Matias" style="display:block; width:42mm; margin:0 auto 1mm;">
                <?php endif; ?>
                <div style="font-size:4.05mm; font-weight:700;">DR. Marcelo Marsillac Matias</div>
                <div style="font-size:3.45mm; margin-top:2.2mm;">Presidente do Simers</div>
            </div>
        </div>
    </div>
</div>
