<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Doacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/DoacaoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/CertificadoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/RankingCacheServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/DoacaoService.php';

use app\modules\common\models\Doacao;
use app\modules\common\services\DoacaoService;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\db\Transaction;
use yii\web\Application;

class DoacaoServiceTest extends TestCase
{
    private $oldDb;

    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }
    }

    protected function tearDown(): void
    {
        if ($this->oldDb !== null) {
            Yii::$app->set('db', $this->oldDb);
            $this->oldDb = null;
        }

        parent::tearDown();
    }

    public function testUpdateResetaStatusParaPendenteELimpaMotivoReprovado(): void
    {
        $service = $this->makeServiceWithSaveModelMock();

        $model = new TestDoacao();
        $model->status = Doacao::STATUS_REJEITADA;
        $model->motivo_reprovado = 'Documento invalido';

        $service->update($model);

        $this->assertSame(Doacao::STATUS_PENDENTE, $model->status);
        $this->assertNull($model->motivo_reprovado);
    }

    public function testDeleteFailsWhenDoacaoIsPendente(): void
    {
        $service = $this->makeService();
        $model = new TestDoacao();
        $model->status = Doacao::STATUS_PENDENTE;

        $this->assertFalse($service->delete($model));
        $this->assertSame('Somente doacoes rejeitadas podem ser excluidas.', $model->getFirstError('status'));
        $this->assertSame(0, $model->deleteCalls);
    }

    public function testDeleteFailsWhenDoacaoIsAprovada(): void
    {
        $service = $this->makeService();
        $model = new TestDoacao();
        $model->status = Doacao::STATUS_APROVADA;

        $this->assertFalse($service->delete($model));
        $this->assertSame('Somente doacoes rejeitadas podem ser excluidas.', $model->getFirstError('status'));
        $this->assertSame(0, $model->deleteCalls);
    }

    public function testDeleteFailsWhenDoacaoHasCertificadoEmitido(): void
    {
        $service = $this->makeService(true);
        $model = new TestDoacao();
        $model->status = Doacao::STATUS_REJEITADA;
        $model->participacao_id = 123;

        $this->assertFalse($service->delete($model));
        $this->assertSame('Doacoes com certificado emitido nao podem ser excluidas.', $model->getFirstError('status'));
        $this->assertSame(0, $model->deleteCalls);
    }

    public function testDeleteSucceedsWhenDoacaoIsRejeitadaAndHasNoCertificado(): void
    {
        $transaction = $this->getMockBuilder(Transaction::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['commit', 'rollBack'])
            ->getMock();

        $transaction->expects($this->once())
            ->method('commit');

        $transaction->expects($this->never())
            ->method('rollBack');

        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['beginTransaction'])
            ->getMock();

        $db->expects($this->once())
            ->method('beginTransaction')
            ->willReturn($transaction);

        $this->oldDb = Yii::$app->db;
        Yii::$app->set('db', $db);

        $model = new TestDoacao();
        $model->status = Doacao::STATUS_REJEITADA;
        $model->participacao_id = 0;
        $model->arquivo = null;
        $model->deleteResult = 1;

        $service = $this->makeService(false);

        $this->assertTrue($service->delete($model));
        $this->assertSame([], $model->getErrors());
        $this->assertSame(1, $model->deleteCalls);
    }

    private function makeService(bool $hasCertificado = false): DoacaoService
    {
        $certificadoService = $this->createMock(CertificadoServiceInterface::class);
        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);

        return new TestDoacaoService($certificadoService, $rankingCacheService, $hasCertificado);
    }

    private function makeServiceWithSaveModelMock(): DoacaoService
    {
        $certificadoService = $this->createMock(CertificadoServiceInterface::class);
        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);

        return new TestDoacaoServiceWithSaveModelMock($certificadoService, $rankingCacheService);
    }
}

class TestDoacao extends Doacao
{
    public $status;
    public $participacao_id;
    public $arquivo;
    public $motivo_reprovado;
    public $deleteResult = 1;
    public $deleteCalls = 0;

    public function delete()
    {
        $this->deleteCalls++;
        return $this->deleteResult;
    }
}

class TestDoacaoServiceWithSaveModelMock extends DoacaoService
{
    protected function saveModel(Doacao $model, bool $isNew): bool
    {
        return true;
    }
}

class TestDoacaoService extends DoacaoService
{
    private bool $hasCertificado;

    public function __construct(
        CertificadoServiceInterface $certificadoService,
        RankingCacheServiceInterface $rankingCacheService,
        bool $hasCertificado
    ) {
        parent::__construct($certificadoService, $rankingCacheService);
        $this->hasCertificado = $hasCertificado;
    }

    protected function hasCertificadoEmitido(Doacao $model): bool
    {
        return $this->hasCertificado;
    }
}
