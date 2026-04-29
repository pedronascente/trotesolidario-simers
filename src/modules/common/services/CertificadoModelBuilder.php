<?php

namespace app\modules\common\services;

use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;

/**
 * Responsável por construir o modelo legado do certificado para renderização
 */
class CertificadoModelBuilder
{
    public function build(Certificado $certificado): array
    {
        $participacao = $certificado->participacao;
        $trote = $participacao->trote ?? null;
        $participante = $participacao->user ?? null;

        $totalHoras = $this->calculateTotalHoras($certificado, $participacao);
        $tipos = $this->extractDonationTypes($participacao);
        $tipoPrincipal = $tipos[0] ?? '';
        $temComissao = $this->hasComissaoMember($tipos);

        $dataInicioExtenso = $this->formatarDataExtenso($trote->data_inicio ?? null);
        $dataFimExtenso = $this->formatarDataExtenso($trote->data_fim ?? null);

        return [
            'name' => $this->normalize($participante->nome ?? '-') ?: '-',
            'trote' => str_replace('.', '/', (string) ($trote->edicao ?? '-')),
            'tipo_doacao' => $tipoPrincipal,
            'all_donations' => $tipos,
            'total_horas' => $totalHoras,
            'frase_certificado' => 'nos dias ' . $dataInicioExtenso . ' à ' . $dataFimExtenso . ', com carga horária total de',
            'qualidade' => $temComissao ? 'MEMBRO DA COMISSÃO ORGANIZADORA' : 'PARTICIPANTE',
            'codigo_validador' => $this->normalize($certificado->codigo_validador ?? ''),
            'data_inicio_extenso' => $dataInicioExtenso,
            'data_fim_extenso' => $dataFimExtenso,
            'universidade' => $this->normalize($participacao->universidade->nome ?? ''),
        ];
    }

    private function calculateTotalHoras(Certificado $certificado, $participacao): int
    {
        $total = 0;
        $tiposSomados = [];

        foreach (($participacao->doacoes ?? []) as $doacao) {
            if (($doacao->status ?? null) !== Doacao::STATUS_APROVADA || $doacao->tipoDoacao === null) {
                continue;
            }

            $tipoId = (int) $doacao->tipoDoacao->id;
            if (in_array($tipoId, $tiposSomados, true)) {
                continue;
            }

            $tiposSomados[] = $tipoId;
            $total += (int) ($doacao->tipoDoacao->carga_horaria ?? 0);
        }

        if ($total <= 0) {
            $total = (int) ($certificado->carga_horaria_total ?? 0);
        }

        return $total;
    }

    private function extractDonationTypes($participacao): array
    {
        $tipos = [];

        foreach (($participacao->doacoes ?? []) as $doacao) {
            if (($doacao->status ?? null) !== Doacao::STATUS_APROVADA) {
                continue;
            }

            $tipo = $doacao->tipoDoacao ?? null;
            if ($tipo === null) {
                continue;
            }

            $nomeTipo = $this->normalize((string) $tipo->nome);
            if ($nomeTipo !== '' && !in_array($nomeTipo, $tipos, true)) {
                $tipos[] = $nomeTipo;
            }
        }

        return $tipos;
    }

    private function hasComissaoMember(array $tipos): bool
    {
        foreach ($tipos as $tipo) {
            if (stripos($tipo, 'comiss') !== false) {
                return true;
            }
        }

        return false;
    }

    private function formatarDataExtenso(?string $data): string
    {
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
            3 => 'março',
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
    }

    private function normalize(?string $value): string
    {
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
    }
}
