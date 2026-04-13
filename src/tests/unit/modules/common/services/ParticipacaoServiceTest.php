<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/ParticipacaoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/ParticipacaoService.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participacao.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\Participacao;
use app\modules\common\services\ParticipacaoService;
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

        $service = new TestParticipacaoService(false, false, false);
        $model = new FakeParticipacao();
        $model->user_id = 55;
        $model->validateResult = true;

        $this->assertFalse($service->create($model));
        $this->assertSame('Selecione um usuario participante ativo.', $model->getFirstError('user_id'));
    }

    public function testFindUsersReturnsOnlyParticipantesAtivos(): void
    {
        $service = new TestParticipacaoService(false, false, true);
        $users = $service->findUsers();

        $this->assertSame([
            1 => 'Participante Ativo | 111.111.111-11 | participante@example.com',
        ], $users);
    }

    public function testDeleteFailsWhenParticipacaoHasDoacoes(): void
    {
        $service = new TestParticipacaoService(true, false, true);
        $model = new FakeParticipacao();
        $model->id = 10;
        $model->fakeIsNewRecord = false;

        $this->expectExceptionMessage('Nao e possivel excluir esta participacao porque existem doacoes vinculadas a ela.');
        $service->delete($model);
    }

    public function testDeleteFailsWhenParticipacaoHasCertificado(): void
    {
        $service = new TestParticipacaoService(false, true, true);
        $model = new FakeParticipacao();
        $model->id = 11;
        $model->fakeIsNewRecord = false;

        $this->expectExceptionMessage('Nao e possivel excluir esta participacao porque existe certificado vinculado a ela.');
        $service->delete($model);
    }

    public function testDeleteSucceedsWhenParticipacaoHasNoDependencias(): void
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

        $service = new TestParticipacaoService(false, false, true);
        $model = new FakeParticipacao();
        $model->id = 12;
        $model->fakeIsNewRecord = false;
        $model->deleteResult = 1;

        $this->assertTrue($service->delete($model));
        $this->assertSame(1, $model->deleteCalls);
    }
}

class TestParticipacaoService extends ParticipacaoService
{
    private bool $hasDoacoes;
    private bool $hasCertificados;
    private bool $userAvailable;

    public function __construct(bool $hasDoacoes, bool $hasCertificados, bool $userAvailable)
    {
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

class FakeParticipacao extends Participacao
{
    public $id;
    public $fakeIsNewRecord = false;
    public $deleteResult = 1;
    public $deleteCalls = 0;
    public $validateResult = true;

    public function attributes(): array
    {
        return ['id', 'user_id', 'trote_id', 'universidade_id', 'curso', 'status', 'created_at', 'updated_at'];
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
}

class FakeUserQuery
{
    public function orderBy($order)
    {
        return $this;
    }

    public function all()
    {
        $user = new FakeUser();
        $user->id = 1;
        $user->nome = 'Participante Ativo';
        $user->cpf = '11111111111';
        $user->email = 'participante@example.com';

        return [$user];
    }
}

class FakeUser extends User
{
    public function attributes(): array
    {
        return ['id', 'nome', 'email', 'username', 'cpf', 'password_hash', 'role', 'authKey', 'status', 'created_at', 'updated_at'];
    }
}
