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

$fundo = $asset('FUNDO.png');
$LOGO20242 = $asset('LOGO20242.png');

?>
<style type="text/css">
.tg {}

.tg td {
    font-family: Arial, sans-serif;
    font-size: 14px;
    overflow: hidden;
    padding: 10px 5px;
    word-break: normal;
}

.tg th {
    font-family: Arial, sans-serif;
    font-size: 14px;
    font-weight: normal;
    overflow: hidden;
    padding: 10px 5px;
    word-break: normal;
}

.tg .tg-baqh {
    text-align: center;
    vertical-align: top
}

.tg .tg-0lax {
    text-align: left;
    vertical-align: top
}
</style>

<body class="body">
    <div style="background-color: #4b3c91;padding: 40px 40px 30px 40px;">
        <div class="well"
            style="background-repeat: no-repeat; background-size: cover;background-image: url('<?= $fundo; ?>');background-color: #fefefe; border-radius:36px;">
            <div class="row">
                <table class="tg" style="table-layout: fixed; width: 100%">
                    <thead>
                        <tr>
                            <th class="tg-0lax"></th>
                            <th class="tg-baqh" colspan="3">
                                <img src="<?=$LOGO20242 ?>" style="width:auto;height: 130px;"alt="logo" />
                            </th>
                            <th class="tg-0lax"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-baqh" colspan="3" style="text-align: justify;">
                                <p style="color:#000; font-size: 17px;">O Trote Solidário é um projeto realizado pelo
                                    Núcleo Acadêmico Simers desde 2008. Esta ação é a união da campanha de doação de
                                    sangue realizada pelos ingressantes das universidades de medicina e o convite a
                                    sociedade para doar alimentos às comunidades carentes, que mantém a tradição do
                                    trote universitário, tornando realidade o objetivo de todos os médicos: SALVAR
                                    VIDAS.</p>
                            </td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-baqh" colspan="3" style="text-align: justify;">
                                <p style="color:#000;font-size: 17px;">Como reconhecimento público, o Trote Solidário
                                    foi vencedor do Prêmio Top Cidadania 2013 da ABRH-RS e do Prêmio Ser Humano Oswaldo
                                    Checchia 2014 da ABRH-Nacional, na modalidade Desenvolvimento Sustentável e
                                    Responsabilidade Social/ Organização Cidadã e prêmio Top Cidadania da ABRH-RS, na
                                    categoria organização em 2022.</p>
                            </td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <td class="tg-baqh" colspan="3" style="text-align: justify;">
                                <p style="color:#000;font-size: 17px;">Atualmente o Trote Solidário promove as seguintes
                                    ações: Doação de sangue, coleta de: alimentos, tampinhas plásticas e livros
                                    pré-vestibular para doação junto a entidades carentes.</p>
                            </td>
                            <td class="tg-0lax"></td>
                        </tr>
                        <tr>
                            <td class="tg-0lax"></td>
                            <th class="tg-baqh" colspan="3">
                            </th>
                            <td class="tg-0lax"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
