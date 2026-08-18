<?php

namespace tests\unit\modules\participante\controllers;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/participante/controllers/DefaultController.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/ParticipacaoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/RankingCacheServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\modules\common\models\Participacao;
use app\modules\common\models\ParticipacaoUniversidadeChangeRequest;
use app\modules\common\models\ParticipantUniversityCorrectionForm;
use app\modules\common\models\ParticipantUniversityCorrectionRequestForm;
use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use app\modules\participante\controllers\DefaultController;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\Component;
use yii\base\Module;
use yii\db\Connection;
use yii\web\Application;
use yii\web\Request;
use yii\web\Response;

class DefaultControllerHomeStartParticipationTest extends TestCase
{
    private $oldDb;
    private $oldUser;
    private $oldRequest;
    private $oldResponse;
    private $oldSession;
    private $oldParticipacaoServiceDefinition;
    private $oldRankingServiceDefinition;

    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }

        $this->oldDb = Yii::$app->db;
        $this->oldUser = Yii::$app->user;
        $this->oldRequest = Yii::$app->get('request');
        $this->oldResponse = Yii::$app->get('response');
        $this->oldSession = Yii::$app->get('session');
        $this->oldParticipacaoServiceDefinition = Yii::$container->has(ParticipacaoServiceInterface::class)
            ? Yii::$container->getDefinitions()[ParticipacaoServiceInterface::class]
            : null;
        $this->oldRankingServiceDefinition = Yii::$container->has(RankingCacheServiceInterface::class)
            ? Yii::$container->getDefinitions()[RankingCacheServiceInterface::class]
            : null;

        $db = new Connection([
            'dsn' => 'sqlite::memory:',
        ]);
        $db->open();

        Yii::$app->set('db', $db);
        Yii::$app->set('user', new FakeWebUserForHomeTest(['id' => 99]));
        Yii::$app->set('request', new Request());
        Yii::$app->set('response', new Response());
        Yii::$app->set('session', new FakeSessionForHomeTest());
        Yii::$app->response->format = Response::FORMAT_HTML;

        $this->createSchema($db);
        $this->seedBaseData();
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        if ($this->oldDb !== null) {
            Yii::$app->set('db', $this->oldDb);
            $this->oldDb = null;
        }

        if ($this->oldUser !== null) {
            Yii::$app->set('user', $this->oldUser);
            $this->oldUser = null;
        }

        if ($this->oldRequest !== null) {
            Yii::$app->set('request', $this->oldRequest);
            $this->oldRequest = null;
        }

        if ($this->oldResponse !== null) {
            Yii::$app->set('response', $this->oldResponse);
            $this->oldResponse = null;
        }

        if ($this->oldSession !== null) {
            Yii::$app->set('session', $this->oldSession);
            $this->oldSession = null;
        }

        Yii::$container->clear(ParticipacaoServiceInterface::class);
        if ($this->oldParticipacaoServiceDefinition !== null) {
            Yii::$container->set(ParticipacaoServiceInterface::class, $this->oldParticipacaoServiceDefinition);
        }

        Yii::$container->clear(RankingCacheServiceInterface::class);
        if ($this->oldRankingServiceDefinition !== null) {
            Yii::$container->set(RankingCacheServiceInterface::class, $this->oldRankingServiceDefinition);
        }

        parent::tearDown();
    }

    public function testHomeUsesOnlyActiveUniversitiesInStartParticipationModal(): void
    {
        Yii::$container->set(ParticipacaoServiceInterface::class, static fn() => new FakeParticipacaoServiceForHomeTest());

        $controller = new TestParticipanteDefaultHomeController('default', new Module('participante'));
        $result = $controller->actionHome();

        $this->assertSame('home', $result['view']);
        $this->assertTrue($result['params']['showStartParticipationCard']);
        $this->assertSame([
            10 => 'Universidade Ativa | Cidade A/RS',
        ], $result['params']['startParticipationUniversidades']);
    }

    public function testHomeKeepsModalOpenAndShowsFlashWhenParticipationCreationFails(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'participation_form' => 'start-active-trote',
            'Participacao' => [
                'universidade_id' => 10,
                'curso' => 'Medicina',
            ],
        ];

        $service = new FakeParticipacaoServiceForHomeTest();
        $service->throwOnCreate = true;
        Yii::$container->set(ParticipacaoServiceInterface::class, static fn() => $service);

        $controller = new TestParticipanteDefaultHomeController('default', new Module('participante'));
        $result = $controller->actionHome();

        $this->assertSame('home', $result['view']);
        $this->assertTrue($result['params']['shouldOpenStartParticipationModal']);
        $this->assertSame(
            'Nao foi possivel iniciar sua participacao agora. Tente novamente em instantes.',
            Yii::$app->session->getFlash('error')
        );
    }

    public function testHomeSummaryRespectsSelectedTrote(): void
    {
        $this->insert('trote', [
            'id' => 8,
            'titulo' => 'Outra edição',
            'edicao' => '2026.2',
            'status' => 'ativo',
            'data_inicio' => '2026-07-01',
            'data_fim' => '2026-12-31',
        ]);
        $this->insert('participacao', ['id' => 1, 'user_id' => 99, 'trote_id' => 7, 'universidade_id' => 10, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 2, 'user_id' => 99, 'trote_id' => 8, 'universidade_id' => 10, 'status' => 'ativo']);
        $this->insert('doacao', ['id' => 1, 'participacao_id' => 1, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 2, 'participacao_id' => 1, 'status' => 'pendente']);
        $this->insert('doacao', ['id' => 3, 'participacao_id' => 2, 'status' => 'aprovada']);
        $this->insert('certificado', ['id' => 1, 'participacao_id' => 1]);
        $this->insert('certificado', ['id' => 2, 'participacao_id' => 2]);

        $_GET['trote_id'] = '7';
        Yii::$container->set(ParticipacaoServiceInterface::class, static fn() => new FakeParticipacaoServiceForHomeTest());
        Yii::$container->set(RankingCacheServiceInterface::class, static fn() => new FakeRankingServiceForHomeTest());

        $controller = new TestParticipanteDefaultHomeController('default', new Module('participante'));
        $result = $controller->actionHome();

        $this->assertSame(7, $result['params']['selectedTroteId']);
        $this->assertSame([
            'doacoes' => 2,
            'doacoesAprovadas' => 1,
            'doacoesPendentes' => 1,
            'certificados' => 1,
        ], $result['params']['dashboardSummary']);
    }

    private function createSchema(Connection $db): void
    {
        $db->createCommand('CREATE TABLE banner (id INTEGER PRIMARY KEY, ativo INTEGER, tipo TEXT, img_dsk TEXT, img_mob TEXT)')->execute();
        $db->createCommand('CREATE TABLE documentos (id INTEGER PRIMARY KEY, tipo TEXT, nome TEXT, arquivo TEXT)')->execute();
        $db->createCommand('CREATE TABLE trote (id INTEGER PRIMARY KEY, titulo TEXT, edicao TEXT, status TEXT, data_inicio TEXT, data_fim TEXT, descricao TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE participante (id INTEGER PRIMARY KEY, user_id INTEGER, estudante INTEGER, estudante_medicina INTEGER, previsao_formatura TEXT)')->execute();
        $db->createCommand('CREATE TABLE universidade (id INTEGER PRIMARY KEY, nome TEXT, cidade TEXT, uf TEXT, ativo INTEGER, link_doacao_alimento TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE participacao (id INTEGER PRIMARY KEY, user_id INTEGER, trote_id INTEGER, universidade_id INTEGER, curso TEXT, status TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE doacao (id INTEGER PRIMARY KEY, participacao_id INTEGER, tipo_doacao_id INTEGER, status TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE certificado (id INTEGER PRIMARY KEY, participacao_id INTEGER, data_emissao TEXT)')->execute();
    }

    private function seedBaseData(): void
    {
        $this->insert('trote', [
            'id' => 7,
            'titulo' => 'Trote Solidario',
            'edicao' => '2026.1',
            'status' => 'ativo',
            'data_inicio' => '2026-02-01',
            'data_fim' => '2026-06-30',
        ]);

        $this->insert('participante', [
            'id' => 1,
            'user_id' => 99,
            'estudante' => 1,
            'estudante_medicina' => 1,
        ]);

        $this->insert('universidade', [
            'id' => 10,
            'nome' => 'Universidade Ativa',
            'cidade' => 'Cidade A',
            'uf' => 'RS',
            'ativo' => 1,
        ]);

        $this->insert('universidade', [
            'id' => 20,
            'nome' => 'Universidade Inativa',
            'cidade' => 'Cidade B',
            'uf' => 'SC',
            'ativo' => 0,
        ]);
    }

    private function insert(string $table, array $row): void
    {
        Yii::$app->db->createCommand()->insert($table, $row)->execute();
    }
}

class TestParticipanteDefaultHomeController extends DefaultController
{
    public function render($view, $params = [])
    {
        return ['view' => $view, 'params' => $params];
    }
}

class FakeParticipacaoServiceForHomeTest implements ParticipacaoServiceInterface
{
    public bool $throwOnCreate = false;

    public function create(Participacao $model): bool
    {
        if ($this->throwOnCreate) {
            throw new \RuntimeException('falha simulada');
        }

        return true;
    }

    public function update(Participacao $model): bool
    {
        return true;
    }

    public function delete(Participacao $model): bool
    {
        return true;
    }

    public function findModel(int $id): ?Participacao
    {
        return null;
    }

    public function findUsers(): array
    {
        return [];
    }

    public function findTrotes(): array
    {
        return [];
    }

    public function findUniversidades(): array
    {
        return [
            10 => 'Universidade Ativa | Cidade A/RS',
            20 => 'Universidade Inativa | Cidade B/SC',
        ];
    }

    public function findActiveParticipationsByUserId(int $userId): array
    {
        return [];
    }

    public function getParticipantUniversitySelfCorrectionData(int $userId, ?int $participacaoId = null): array
    {
        return [];
    }

    public function selfCorrectParticipantUniversity(int $userId, int $participacaoId, ParticipantUniversityCorrectionForm $form): Participacao
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function getParticipantUniversityCorrectionRequestData(int $userId, ?int $participacaoId = null): array
    {
        return [];
    }

    public function submitParticipantUniversityCorrectionRequest(int $userId, int $participacaoId, ParticipantUniversityCorrectionRequestForm $form): ParticipacaoUniversidadeChangeRequest
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function findUniversityCorrectionRequest(int $id): ?ParticipacaoUniversidadeChangeRequest
    {
        return null;
    }

    public function findUniversityCorrectionRequests(?string $status = null, int $limit = 100): array
    {
        return [];
    }

    public function approveUniversityCorrectionRequest(int $requestId, int $reviewedBy, ?string $reviewNotes = null): ParticipacaoUniversidadeChangeRequest
    {
        throw new \BadMethodCallException('Not implemented.');
    }

    public function rejectUniversityCorrectionRequest(int $requestId, int $reviewedBy, ?string $reviewNotes = null): ParticipacaoUniversidadeChangeRequest
    {
        throw new \BadMethodCallException('Not implemented.');
    }
}

class FakeRankingServiceForHomeTest implements RankingCacheServiceInterface
{
    public function findTrotes(): array
    {
        return [];
    }

    public function rebuild(?int $troteId = null): int
    {
        return 0;
    }

    public function getUniversityRanking(?int $troteId = null): array
    {
        return [];
    }
}

class FakeWebUserForHomeTest extends Component
{
    public int $id;
}

class FakeSessionForHomeTest extends Component
{
    private array $flashes = [];

    public function setFlash($key, $value = true, $removeAfterAccess = true): void
    {
        $this->flashes[$key] = $value;
    }

    public function getFlash($key, $defaultValue = null, $delete = true)
    {
        if (!array_key_exists($key, $this->flashes)) {
            return $defaultValue;
        }

        $value = $this->flashes[$key];
        if ($delete) {
            unset($this->flashes[$key]);
        }

        return $value;
    }
}
