<?php

namespace app\modules\common\services;

use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;
use app\modules\common\models\Participacao;
use RuntimeException;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use Yii;

class CertificadoService implements CertificadoServiceInterface
{
    private PdfGeneratorService $pdfGenerator;
    private CertificadoModelBuilder $modelBuilder;

    public function __construct(
        ?PdfGeneratorService $pdfGenerator = null,
        ?CertificadoModelBuilder $modelBuilder = null
    ) {
        $this->pdfGenerator = $pdfGenerator ?? new PdfGeneratorService();
        $this->modelBuilder = $modelBuilder ?? new CertificadoModelBuilder();
    }
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
        $relativePath = $this->buildRelativePdfPath($certificado);
        $fileName = basename($relativePath);
        $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;

        $pages = $this->renderCertificatePages($certificado, 'pdf');
        $this->pdfGenerator->generatePdf($fullPath, $pages);
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
        return $this->modelBuilder->build($certificado);
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


