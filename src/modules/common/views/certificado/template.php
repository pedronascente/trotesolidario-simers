<?php

use app\modules\common\models\Doacao;

$renderMode = isset($renderMode) && $renderMode === 'pdf' ? 'pdf' : 'web';

$participacao = $model->participacao;
$trote = $participacao->trote ?? null;
$participante = $participacao->user ?? null;

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

$formatarDataExtenso = static function (?string $data): string {
    if (empty($data)) {
        return '-';
    }

    $timestamp = strtotime($data);
    if ($timestamp === false) {
        return '-';
    }

    $meses = [
        1 => 'janeiro',
        2 => 'fevereiro',
        3 => html_entity_decode('mar&ccedil;o', ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        4 => 'abril',
        5 => 'maio',
        6 => 'junho',
        7 => 'julho',
        8 => 'agosto',
        9 => 'setembro',
        10 => 'outubro',
        11 => 'novembro',
        12 => 'dezembro',
    ];

    $dia = (int) date('d', $timestamp);
    $mes = $meses[(int) date('n', $timestamp)] ?? date('m', $timestamp);
    $ano = date('Y', $timestamp);

    return $dia . ' de ' . $mes . ' de ' . $ano;
};

$approvedDoacoes = [];
foreach (($participacao->doacoes ?? []) as $doacao) {
    if (($doacao->status ?? null) === Doacao::STATUS_APROVADA) {
        $approvedDoacoes[] = $doacao;
    }
}

$tipos = [];
$totalHoras = 0;
$temComissao = false;
$tiposSomados = [];
$tipoPrincipal = '';

foreach ($approvedDoacoes as $doacao) {
    $tipo = $doacao->tipoDoacao ?? null;
    if ($tipo === null) {
        continue;
    }

    $nomeTipo = $normalize((string) $tipo->nome);
    if ($nomeTipo === '') {
        continue;
    }

    if ($tipoPrincipal === '') {
        $tipoPrincipal = $nomeTipo;
    }

    if (!in_array($nomeTipo, $tipos, true)) {
        $tipos[] = $nomeTipo;
    }

    $tipoId = (int) ($tipo->id ?? 0);
    if ($tipoId > 0 && !in_array($tipoId, $tiposSomados, true)) {
        $tiposSomados[] = $tipoId;
        $totalHoras += (int) ($tipo->carga_horaria ?? 0);
    }

    if (stripos($nomeTipo, 'comiss') !== false) {
        $temComissao = true;
    }
}

if ($totalHoras <= 0) {
    $totalHoras = (int) ($model->carga_horaria_total ?? 0);
}

$dataInicioExtenso = $formatarDataExtenso($trote->data_inicio ?? null);
$dataFimExtenso = $formatarDataExtenso($trote->data_fim ?? null);

$legacyModel = [
    'name' => $normalize($participante->nome ?? '-') ?: '-',
    'trote' => str_replace('.', '/', (string) ($trote->edicao ?? '-')),
    'tipo_doacao' => $tipoPrincipal,
    'all_donations' => $tipos,
    'total_horas' => $totalHoras,
    'frase_certificado' => 'nos dias ' . $dataInicioExtenso . ' ' . html_entity_decode('&agrave;', ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' ' . $dataFimExtenso . ', com carga hor?ria total de',
    'qualidade' => $temComissao ? html_entity_decode('MEMBRO DA COMISS&Atilde;O ORGANIZADORA', ENT_QUOTES | ENT_HTML5, 'UTF-8') : 'PARTICIPANTE',
    'codigo_validador' => $normalize($model->codigo_validador ?? ''),
    'data_inicio_extenso' => $dataInicioExtenso,
    'data_fim_extenso' => $dataFimExtenso,
    'universidade' => $normalize($participacao->universidade->nome ?? ''),
];

$edicaoBase = preg_replace('/[^0-9]/', '', $legacyModel['trote']);
$basePath = Yii::getAlias('@app/modules/participante/views/certificado');

$firstPageCandidates = [];
$secondPageCandidates = [];

if ($edicaoBase !== '') {
    $firstPageCandidates[] = $basePath . '/certificado' . $edicaoBase . '1.php';
    $secondPageCandidates[] = $basePath . '/certificado' . $edicaoBase . '2.php';
}

// if ($legacyModel['trote'] === '2025/2') {
//     $firstPageCandidates = array_merge([$basePath . '/certificado202521.php'], $firstPageCandidates);
//     $secondPageCandidates = array_merge([$basePath . '/certificado202511.php'], $secondPageCandidates);
// }

// $firstPageCandidates[] = $basePath . '/certificado.php';
// $secondPageCandidates[] = $basePath . '/certificado2.php';

$findFirstExisting = static function (array $paths): ?string {
    foreach ($paths as $path) {
        if (is_file($path)) {
            return $path;
        }
    }

    return null;
};

$pageOne = $findFirstExisting($firstPageCandidates);
$pageTwo = $findFirstExisting($secondPageCandidates);

// echo $pageOne .'<br>  ';
// echo $pageTwo .'<br>  ';



if ($pageOne === null) {
    throw new \RuntimeException(html_entity_decode('Template de certificado n&atilde;o encontrado.', ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

echo $this->renderFile($pageOne, ['model' => $legacyModel, 'renderMode' => $renderMode]);

if ($pageTwo !== null) {
    echo '<pagebreak />';
    echo $this->renderFile($pageTwo, ['model' => $legacyModel, 'renderMode' => $renderMode]);
}
