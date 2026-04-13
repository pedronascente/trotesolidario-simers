<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 5) . '/modules/common/models/Certificado.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Doacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/TipoDoacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Universidade.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/CertificadoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/CertificadoService.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;
use app\modules\common\models\Participacao;
use app\modules\common\models\TipoDoacao;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\services\CertificadoService;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\web\Application;

class CertificadoServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }
    }

    public function testBuildLegacyCertificateModelUsesUniqueApprovedDoacoesAndUtf8Strings(): void
    {
        $service = new ExposedCertificadoService();

        $participante = new FakeUser();
        $participante->nome = 'Ana Participante';

        $trote = new FakeTrote();
        $trote->edicao = '2026/1';
        $trote->data_inicio = '2026-03-01';
        $trote->data_fim = '2026-04-30';

        $universidade = new FakeUniversidade();
        $universidade->nome = 'Universidade Teste';

        $tipoComissao = new FakeTipoDoacao();
        $tipoComissao->id = 10;
        $tipoComissao->nome = 'Comissão Organizadora';
        $tipoComissao->carga_horaria = 10;

        $tipoSangue = new FakeTipoDoacao();
        $tipoSangue->id = 20;
        $tipoSangue->nome = 'Sangue';
        $tipoSangue->carga_horaria = 5;

        $doacaoComissao = new FakeDoacao();
        $doacaoComissao->status = Doacao::STATUS_APROVADA;
        $doacaoComissao->populateRelation('tipoDoacao', $tipoComissao);

        $doacaoSangueA = new FakeDoacao();
        $doacaoSangueA->status = Doacao::STATUS_APROVADA;
        $doacaoSangueA->populateRelation('tipoDoacao', $tipoSangue);

        $doacaoSangueB = new FakeDoacao();
        $doacaoSangueB->status = Doacao::STATUS_APROVADA;
        $doacaoSangueB->populateRelation('tipoDoacao', $tipoSangue);

        $doacaoRejeitada = new FakeDoacao();
        $doacaoRejeitada->status = Doacao::STATUS_REJEITADA;
        $doacaoRejeitada->populateRelation('tipoDoacao', $tipoSangue);

        $participacao = new FakeParticipacao();
        $participacao->populateRelation('user', $participante);
        $participacao->populateRelation('trote', $trote);
        $participacao->populateRelation('universidade', $universidade);
        $participacao->populateRelation('doacoes', [$doacaoComissao, $doacaoSangueA, $doacaoSangueB, $doacaoRejeitada]);

        $certificado = new FakeCertificado();
        $certificado->codigo_validador = 'CERT-20261-1';
        $certificado->carga_horaria_total = 99;
        $certificado->populateRelation('participacao', $participacao);

        $legacy = $service->exposeBuildLegacyCertificateModel($certificado);

        $this->assertSame('Ana Participante', $legacy['name']);
        $this->assertSame(['Comissão Organizadora', 'Sangue'], $legacy['all_donations']);
        $this->assertSame(15, $legacy['total_horas']);
        $this->assertSame('MEMBRO DA COMISSÃO ORGANIZADORA', $legacy['qualidade']);
        $this->assertStringContainsString('1 de março de 2026 à 30 de abril de 2026', $legacy['frase_certificado']);
    }

    public function testResolveCertificateTemplatePathsUsesModern2025v2Templates(): void
    {
        $service = new ExposedCertificadoService();

        [$pageOne, $pageTwo] = $service->exposeResolveCertificateTemplatePaths('2025/2');

        $this->assertSame('certificado202521.php', basename($pageOne));
        $this->assertSame('certificado202511.php', basename((string) $pageTwo));
    }

    public function testSanitizeHtmlForPdfRemovesControlCharacters(): void
    {
        $service = new ExposedCertificadoService();
        $html = "<div>Olá" . chr(7) . " mundo</div>";

        $sanitized = $service->exposeSanitizeHtmlForPdf($html);

        $this->assertStringNotContainsString(chr(7), $sanitized);
        $this->assertStringContainsString('Olá mundo', $sanitized);
    }
}

class ExposedCertificadoService extends CertificadoService
{
    public function exposeBuildLegacyCertificateModel(Certificado $certificado): array
    {
        return $this->buildLegacyCertificateModel($certificado);
    }

    public function exposeResolveCertificateTemplatePaths(string $troteEdicao): array
    {
        return $this->resolveCertificateTemplatePaths($troteEdicao);
    }

    public function exposeSanitizeHtmlForPdf(string $html): string
    {
        return $this->sanitizeHtmlForPdf($html);
    }
}

class FakeCertificado extends Certificado
{
    public function attributes(): array
    {
        return ['id', 'participacao_id', 'codigo_validador', 'carga_horaria_total', 'arquivo_pdf', 'data_emissao', 'hash_integridade', 'emitido_por'];
    }
}

class FakeParticipacao extends Participacao
{
    public function attributes(): array
    {
        return ['id', 'user_id', 'trote_id', 'universidade_id', 'curso', 'status', 'created_at', 'updated_at'];
    }
}

class FakeDoacao extends Doacao
{
    public function attributes(): array
    {
        return ['id', 'participacao_id', 'tipo_doacao_id', 'evento_id', 'arquivo', 'status', 'motivo_reprovado', 'validado_por', 'validado_em', 'cpf_snapshot', 'edicao_snapshot', 'created_at', 'updated_at'];
    }
}

class FakeTipoDoacao extends TipoDoacao
{
    public function attributes(): array
    {
        return ['id', 'nome', 'descricao', 'carga_horaria', 'created_at', 'updated_at'];
    }
}

class FakeTrote extends Trote
{
    public function attributes(): array
    {
        return ['id', 'titulo', 'edicao', 'descricao', 'status', 'data_inicio', 'data_fim', 'created_at', 'updated_at'];
    }
}

class FakeUniversidade extends Universidade
{
    public function attributes(): array
    {
        return ['id', 'nome', 'sigla', 'cidade', 'estado', 'created_at', 'updated_at'];
    }
}

class FakeUser extends User
{
    public function attributes(): array
    {
        return ['id', 'nome', 'email', 'username', 'cpf', 'password_hash', 'role', 'status', 'created_at', 'updated_at'];
    }
}
