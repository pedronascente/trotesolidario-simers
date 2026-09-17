<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/ParticipacaoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/CertificadoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/RankingCacheServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/ParticipacaoService.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participacao.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\Participacao;
use app\modules\common\services\ParticipacaoService;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class ParticipacaoServiceTest extends TestCase
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

    public function testCreateFailsWhenUserIsNotParticipanteAtivo(): void
    {
        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['transaction'])
            ->getMock();
        $db->method('transaction')->willReturnCallback(function ($callback) {
            return $callback();
        });

        $this->oldDb = Yii::$app->db;
        Yii::$app->set('db', $db);

        $service = $this->makeService(false, false, false);
        $model = new ParticipacaoServiceFakeParticipacao();
        $model->user_id = 55;
        $model->validateResult = true;

        $this->assertFalse($service->create($model));
        $this->assertSame('Selecione um usuario participante ativo.', $model->getFirstError('user_id'));
    }

    public function testFindUsersReturnsOnlyParticipantesAtivos(): void
    {
        $service = $this->makeService(false, false, true);
        $users = $service->findUsers();

        $this->assertSame([
            1 => 'Participante Ativo | 111.111.111-11 | participante@example.com',
        ], $users);
    }

    public function testDeleteFailsWhenParticipacaoHasDoacoes(): void
    {
        $service = $this->makeService(true, false, true);
        $model = new ParticipacaoServiceFakeParticipacao();
        $model->id = 10;
        $model->fakeIsNewRecord = false;

        $this->expectExceptionMessage('Nao e possivel excluir esta participacao porque existem doacoes vinculadas a ela.');
        $service->delete($model);
    }

    public function testDeleteFailsWhenParticipacaoHasCertificado(): void
    {
        $service = $this->makeService(false, true, true);
        $model = new ParticipacaoServiceFakeParticipacao();
        $model->id = 11;
        $model->fakeIsNewRecord = false;

        $this->expectExceptionMessage('Nao e possivel excluir esta participacao porque existe certificado vinculado a ela.');
        $service->delete($model);
    }

    public function testCreateTriggersRankingRebuildForParticipationTrote(): void
    {
        $db = $this->mockTransactionalDb();
        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->once())
            ->method('rebuild')
            ->with(7);

        $service = $this->makeService(false, false, true, $rankingCacheService);
        $model = new ParticipacaoServiceFakeParticipacao();
        $model->user_id = 20;
        $model->trote_id = 7;
        $model->validateResult = true;
        $model->saveResult = true;

        $this->assertTrue($service->create($model));
        $this->assertSame(1, $model->saveCalls);
    }

    public function testUpdateTriggersRankingRebuildForOldAndNewTrotes(): void
    {
        $db = $this->mockTransactionalDb();
        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->exactly(2))
            ->method('rebuild')
            ->withConsecutive([4], [9]);

        $service = $this->makeService(false, false, true, $rankingCacheService);
        $model = new ParticipacaoServiceFakeParticipacao();
        $model->id = 30;
        $model->user_id = 20;
        $model->trote_id = 9;
        $model->fakeIsNewRecord = false;
        $model->validateResult = true;
        $model->saveResult = true;
        $model->oldAttributesMap = ['trote_id' => 4];

        $this->assertTrue($service->update($model));
        $this->assertSame(1, $model->saveCalls);
    }

    public function testDeleteTriggersRankingRebuildForParticipationTrote(): void
    {
        $db = $this->mockTransactionalDb();
        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->once())
            ->method('rebuild')
            ->with(12);

        $service = $this->makeService(false, false, true, $rankingCacheService);
        $model = new ParticipacaoServiceFakeParticipacao();
        $model->id = 12;
        $model->trote_id = 12;
        $model->fakeIsNewRecord = false;
        $model->deleteResult = 1;

        $this->assertTrue($service->delete($model));
        $this->assertSame(1, $model->deleteCalls);
    }

    public function testDeleteSucceedsWhenParticipacaoHasNoDependencias(): void
    {
        $db = $this->mockTransactionalDb();
        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->once())
            ->method('rebuild')
            ->with(15);

        $service = $this->makeService(false, false, true, $rankingCacheService);
        $model = new ParticipacaoServiceFakeParticipacao();
        $model->id = 12;
        $model->trote_id = 15;
        $model->fakeIsNewRecord = false;
        $model->deleteResult = 1;

        $this->assertTrue($service->delete($model));
        $this->assertSame(1, $model->deleteCalls);
    }

    private function makeService(
        bool $hasDoacoes,
        bool $hasCertificados,
        bool $userAvailable,
        ?RankingCacheServiceInterface $rankingCacheService = null
    ): TestParticipacaoService {
        return new TestParticipacaoService(
            $rankingCacheService ?? $this->createMock(RankingCacheServiceInterface::class),
            $this->createMock(CertificadoServiceInterface::class),
            $hasDoacoes,
            $hasCertificados,
            $userAvailable
        );
    }

    private function mockTransactionalDb(): Connection
    {
        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['transaction'])
            ->getMock();
        $db->method('transaction')->willReturnCallback(function ($callback) {
            return $callback();
        });

        $this->oldDb = Yii::$app->db;
        Yii::$app->set('db', $db);

        return $db;
    }
}

class TestParticipacaoService extends ParticipacaoService
{
    private bool $hasDoacoes;
    private bool $hasCertificados;
    private bool $userAvailable;

    public function __construct(
        RankingCacheServiceInterface $rankingCacheService,
        CertificadoServiceInterface $certificadoService,
        bool $hasDoacoes,
        bool $hasCertificados,
        bool $userAvailable
    )
    {
        parent::__construct($rankingCacheService, $certificadoService);
        $this->hasDoacoes = $hasDoacoes;
        $this->hasCertificados = $hasCertificados;
        $this->userAvailable = $userAvailable;
    }

    protected function hasDoacoesVinculadas(int $participacaoId): bool
    {
        return $this->hasDoacoes;
    }

    protected function hasCertificadosVinculados(int $participacaoId): bool
    {
        return $this->hasCertificados;
    }

    protected function isUserAvailableForParticipacao(int $userId): bool
    {
        return $this->userAvailable;
    }

    protected function findAvailableUsersQuery()
    {
        return new FakeUserQuery();
    }
}

class ParticipacaoServiceFakeParticipacao extends Participacao
{
    public $id;
    public $user_id;
    public $trote_id;
    public $universidade_id;
    public $fakeIsNewRecord = false;
    public $saveResult = true;
    public $saveCalls = 0;
    public $deleteResult = 1;
    public $deleteCalls = 0;
    public $validateResult = true;
    public $oldAttributesMap = [];

    public function attributes(): array
    {
        return ['id', 'user_id', 'trote_id', 'universidade_id', 'curso', 'status', 'created_at', 'updated_at'];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $this->saveCalls++;
        return $this->saveResult;
    }

    public function delete()
    {
        $this->deleteCalls++;
        return $this->deleteResult;
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->validateResult;
    }

    public function getIsNewRecord()
    {
        return $this->fakeIsNewRecord;
    }

    public function getOldAttribute($name)
    {
        return $this->oldAttributesMap[$name] ?? null;
    }
}

class FakeUserQuery
{
    public function orderBy($order)
    {
        return $this;
    }

    public function all()
    {
        $user = new ParticipacaoServiceFakeUser();
        $user->id = 1;
        $user->nome = 'Participante Ativo';
        $user->cpf = '11111111111';
        $user->email = 'participante@example.com';

        return [$user];
    }
}

class ParticipacaoServiceFakeUser extends User
{
    public function attributes(): array
    {
        return ['id', 'nome', 'email', 'username', 'cpf', 'password_hash', 'role', 'authKey', 'status', 'created_at', 'updated_at'];
    }
}
