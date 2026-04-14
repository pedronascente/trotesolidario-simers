<?php

namespace app\modules\common\services;

use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;
use app\modules\common\models\Participacao;
use RuntimeException;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Yii;

class CertificadoService implements CertificadoServiceInterface
{
    public function syncFromApprovedDoacao(Doacao $doacao, int $adminUserId): Certificado
    {
        if ($doacao->status !== Doacao::STATUS_APROVADA) {
            throw new \RuntimeException('Apenas doacoes aprovadas podem gerar certificado.');
        }

        $participacao = $this->findParticipacao($doacao->participacao_id);
        $certificado = Certificado::findOne(['participacao_id' => $participacao->id]);

        if ($certificado === null) {
            $certificado = new Certificado();
            $certificado->participacao_id = $participacao->id;
            $certificado->codigo_validador = $this->generateCodigoValidador($participacao->id, $participacao->trote->edicao ?? 'SEM');
        }

        $certificado->carga_horaria_total = $this->calculateCargaHorariaTotal($participacao);
        $certificado->data_emissao = date('Y-m-d H:i:s');
        $certificado->emitido_por = $adminUserId;
        $certificado->arquivo_pdf = $this->buildRelativePdfPath($certificado);
        $certificado->hash_integridade = $this->generateHashIntegridade($certificado, $participacao);

        $this->saveCertificado($certificado);
        $this->generatePdf($certificado);

        return $certificado;
    }

    public function ensurePdf(Certificado $certificado, bool $force = false): Certificado
    {
        if (!$force && $certificado->getArquivoPdfPath() !== null) {
            return $certificado;
        }

        $participacao = $this->findParticipacao($certificado->participacao_id);

        if (empty($certificado->codigo_validador)) {
            $certificado->codigo_validador = $this->generateCodigoValidador($participacao->id, $participacao->trote->edicao ?? 'SEM');
        }

        if (empty($certificado->data_emissao)) {
            $certificado->data_emissao = date('Y-m-d H:i:s');
        }

        if (empty($certificado->emitido_por)) {
            $certificado->emitido_por = Yii::$app->user->id ?? 1;
        }

        $certificado->carga_horaria_total = $this->calculateCargaHorariaTotal($participacao);
        $certificado->arquivo_pdf = $this->buildRelativePdfPath($certificado);
        $certificado->hash_integridade = $this->generateHashIntegridade($certificado, $participacao);

        $this->saveCertificado($certificado);
        $this->generatePdf($certificado);

        return $certificado;
    }

    private function findParticipacao(int $participacaoId): Participacao
    {
        $participacao = Participacao::find()
            ->with(['user', 'trote', 'universidade', 'doacoes.tipoDoacao'])
            ->where(['id' => $participacaoId])
            ->one();

        if ($participacao === null) {
            throw new \RuntimeException('Participacao nao encontrada para gerar certificado.');
        }

        return $participacao;
    }

    private function calculateCargaHorariaTotal(Participacao $participacao): int
    {
        $total = 0;
        $tiposSomados = [];

        foreach ($participacao->doacoes as $doacao) {
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

        return $total;
    }

    protected function generatePdf(Certificado $certificado): void
    {
        $certificado = Certificado::find()
            ->with(['participacao.user', 'participacao.trote', 'participacao.universidade', 'participacao.doacoes.tipoDoacao', 'emissor'])
            ->where(['id' => $certificado->id])
            ->one() ?? $certificado;

        $directory = Yii::getAlias('@pdf') . DIRECTORY_SEPARATOR . 'certificados';
        $this->ensureDirectoryExists($directory);

        $relativePath = $this->buildRelativePdfPath($certificado);
        $fileName = basename($relativePath);
        $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;
        $tempDir = Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'mpdf';
        $this->ensureDirectoryExists($tempDir);

        $previousMemoryLimit = ini_get('memory_limit');
        $previousMaxExecutionTime = ini_get('max_execution_time');
        @ini_set('memory_limit', '256M');
        @set_time_limit(120);

        try {
            $pages = $this->renderCertificatePages($certificado, 'pdf');

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L',
                'tempDir' => $tempDir,
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'default_font' => 'Arial',
            ]);
            $mpdf->showImageErrors = true;
            $mpdf->autoScriptToLang = false;
            $mpdf->autoLangToFont = false;
            $mpdf->SetTitle('Certificado Trote Solidario');

            $bootstrapCssPath = Yii::getAlias('@vendor', false);
            if (is_string($bootstrapCssPath) && $bootstrapCssPath !== '') {
                $bootstrapCssPath .= DIRECTORY_SEPARATOR . 'kartik-v'
                    . DIRECTORY_SEPARATOR . 'yii2-mpdf'
                    . DIRECTORY_SEPARATOR . 'src'
                    . DIRECTORY_SEPARATOR . 'assets'
                    . DIRECTORY_SEPARATOR . 'kv-mpdf-bootstrap.min.css';

                if (is_file($bootstrapCssPath)) {
                    $bootstrapCss = file_get_contents($bootstrapCssPath);
                    if (is_string($bootstrapCss) && $bootstrapCss !== '') {
                        $mpdf->WriteHTML($bootstrapCss, 1);
                    }
                }
            }

            if (class_exists('\\kartik\\mpdf\\Pdf')) {
                $pdfExtraClass = '\\kartik\\mpdf\\Pdf';
                /** @var object $pdfExtra */
                $pdfExtra = new $pdfExtraClass();
                if (method_exists($pdfExtra, 'getCss')) {
                    $extraCss = $pdfExtra->getCss();
                    if (is_string($extraCss) && $extraCss !== '') {
                        $mpdf->WriteHTML($extraCss, 1);
                    }
                }
            }

            foreach ($pages as $index => $pageHtml) {
                $pageHtml = $this->sanitizeHtmlForPdf($pageHtml);
                $mpdf->WriteHTML($pageHtml);

                if ($index < count($pages) - 1) {
                    $mpdf->AddPage();
                }
            }

            $mpdf->Output($fullPath, Destination::FILE);
        } catch (\Throwable $e) {
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }

            throw $e;
        } finally {
            if ($previousMemoryLimit !== false) {
                @ini_set('memory_limit', (string) $previousMemoryLimit);
            }

            if ($previousMaxExecutionTime !== false) {
                @ini_set('max_execution_time', (string) $previousMaxExecutionTime);
            }
        }
    }

    private function renderCertificatePages(Certificado $certificado, string $renderMode): array
    {
        $legacyModel = $this->buildLegacyCertificateModel($certificado);
        [$pageOne, $pageTwo] = $this->resolveCertificateTemplatePaths($legacyModel['trote']);

        $pages = [
            Yii::$app->view->renderFile($pageOne, ['model' => $legacyModel, 'renderMode' => $renderMode]),
        ];

        if ($pageTwo !== null) {
            $pages[] = Yii::$app->view->renderFile($pageTwo, ['model' => $legacyModel, 'renderMode' => $renderMode]);
        }

        return $pages;
    }

    protected function buildLegacyCertificateModel(Certificado $certificado): array
    {
        $participacao = $certificado->participacao;
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
            $totalHoras = (int) ($certificado->carga_horaria_total ?? 0);
        }

        $dataInicioExtenso = $formatarDataExtenso($trote->data_inicio ?? null);
        $dataFimExtenso = $formatarDataExtenso($trote->data_fim ?? null);

        return [
            'name' => $normalize($participante->nome ?? '-') ?: '-',
            'trote' => str_replace('.', '/', (string) ($trote->edicao ?? '-')),
            'tipo_doacao' => $tipoPrincipal,
            'all_donations' => $tipos,
            'total_horas' => $totalHoras,
            'frase_certificado' => 'nos dias ' . $dataInicioExtenso . ' à ' . $dataFimExtenso . ', com carga horária total de',
            'qualidade' => $temComissao ? 'MEMBRO DA COMISSÃO ORGANIZADORA' : 'PARTICIPANTE',
            'codigo_validador' => $normalize($certificado->codigo_validador ?? ''),
            'data_inicio_extenso' => $dataInicioExtenso,
            'data_fim_extenso' => $dataFimExtenso,
            'universidade' => $normalize($participacao->universidade->nome ?? ''),
        ];
    }

    protected function resolveCertificateTemplatePaths(string $troteEdicao): array
    {
        $edicaoBase = preg_replace('/[^0-9]/', '', $troteEdicao);
        $basePath = Yii::getAlias('@app/modules/participante/views/certificado');

        $firstPageCandidates = [];
        $secondPageCandidates = [];

        if ($edicaoBase !== '') {
            $firstPageCandidates[] = $basePath . '/certificado' . $edicaoBase . '1.php';
            $secondPageCandidates[] = $basePath . '/certificado' . $edicaoBase . '2.php';
        }

        if ($troteEdicao === '2025/2') {
            array_unshift($firstPageCandidates, $basePath . '/certificado202521.php');
            array_unshift($secondPageCandidates, $basePath . '/certificado202511.php');
        }

        $firstPageCandidates[] = $basePath . '/certificado.php';
        $secondPageCandidates[] = $basePath . '/certificado2.php';

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

        if ($pageOne === null) {
            throw new RuntimeException('Template de certificado não encontrado.');
        }

        return [$pageOne, $pageTwo];
    }

    protected function sanitizeHtmlForPdf(string $html): string
    {
        if ($html === '') {
            return $html;
        }

        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $html) ?? $html;

        if (!mb_check_encoding($html, 'UTF-8')) {
            $converted = @mb_convert_encoding($html, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252, ASCII');
            if (is_string($converted) && $converted !== '') {
                $html = $converted;
            }
        }

        $iconv = @iconv('UTF-8', 'UTF-8//IGNORE', $html);
        if ($iconv !== false) {
            $html = $iconv;
        }

        return $html;
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (!@mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException(html_entity_decode('N&atilde;o foi poss&iacute;vel preparar o diret&oacute;rio de certificados: ', ENT_QUOTES | ENT_HTML5, 'UTF-8') . $directory);
        }
    }

    private function buildRelativePdfPath(Certificado $certificado): string
    {
        return 'pdf/certificados/certificado-participacao-' . $certificado->participacao_id . '.pdf';
    }

    private function saveCertificado(Certificado $certificado): void
    {
        if (!$certificado->save()) {
            throw new \RuntimeException('Erro ao salvar certificado: ' . json_encode($certificado->errors));
        }
    }

    private function generateCodigoValidador(int $participacaoId, string $edicao): string
    {
        $base = 'CERT-' . preg_replace('/[^A-Za-z0-9]/', '', $edicao) . '-' . $participacaoId;
        $codigo = $base;
        $suffix = 1;

        while (Certificado::find()->where(['codigo_validador' => $codigo])->exists()) {
            $codigo = $base . '-' . str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
            $suffix++;
        }

        return $codigo;
    }

    private function generateHashIntegridade(Certificado $certificado, Participacao $participacao): string
    {
        return hash('sha256', implode('|', [
            $participacao->id,
            $participacao->user_id,
            $participacao->trote_id,
            $participacao->universidade_id,
            $participacao->curso,
            $participacao->trote->data_inicio ?? '',
            $participacao->trote->data_fim ?? '',
            $certificado->codigo_validador,
            $certificado->carga_horaria_total,
            $certificado->data_emissao,
            $certificado->arquivo_pdf ?? '',
        ]));
    }
}


