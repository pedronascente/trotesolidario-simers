<?php

namespace tests\unit\modules\ranking;

require_once dirname(__DIR__, 3) . '/_bootstrap.php';
require_once dirname(__DIR__, 4) . '/modules/administrator/controllers/RankingController.php';
require_once dirname(__DIR__, 4) . '/modules/participante/controllers/DefaultController.php';
require_once dirname(__DIR__, 4) . '/modules/common/services/contracts/RankingCacheServiceInterface.php';

use app\modules\common\services\contracts\RankingCacheServiceInterface;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\Component;
use yii\base\Module;
use yii\db\Connection;
use yii\web\Application;

class RankingPresentationFlowTest extends TestCase
{
    private $oldDb;
    private $oldUser;
    private array $oldQueryParams = [];
    private $oldRankingServiceDefinition;

    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 4) . '/config/test.php');
        }

        $this->oldDb = Yii::$app->db;
        $this->oldUser = Yii::$app->user;
        $this->oldQueryParams = Yii::$app->request->getQueryParams();
        $this->oldRankingServiceDefinition = Yii::$container->has(RankingCacheServiceInterface::class)
            ? Yii::$container->getDefinitions()[RankingCacheServiceInterface::class]
            : null;

        $db = new Connection([
            'dsn' => 'sqlite::memory:',
        ]);
        $db->open();

        Yii::$app->set('db', $db);
        $this->createParticipantSchema($db);
        Yii::$app->set('user', new FakeWebUser(['id' => 99]));
    }

    protected function tearDown(): void
    {
        Yii::$app->request->setQueryParams($this->oldQueryParams);

        if ($this->oldDb !== null) {
            Yii::$app->set('db', $this->oldDb);
            $this->oldDb = null;
        }

        if ($this->oldUser !== null) {
            Yii::$app->set('user', $this->oldUser);
            $this->oldUser = null;
        }

        Yii::$container->clear(RankingCacheServiceInterface::class);
        if ($this->oldRankingServiceDefinition !== null) {
            Yii::$container->set(RankingCacheServiceInterface::class, $this->oldRankingServiceDefinition);
        }

        parent::tearDown();
    }

    public function testAdministratorRankingIndexPassesSelectedTroteAndUniversityRankingToView(): void
    {
        Yii::$app->request->setQueryParams(['trote_id' => 7]);

        $service = $this->createMock(RankingCacheServiceInterface::class);
        $service->expects($this->once())
            ->method('findTrotes')
            ->willReturn([7 => 'Trote Solidario | 2026.1']);
        $service->expects($this->once())
            ->method('getUniversityRanking')
            ->with(7)
            ->willReturn([
                ['universidade_id' => 1, 'nome' => 'Universidade A', 'pontos' => 200, 'participantes' => 2],
            ]);

        $controller = new TestAdministratorRankingController('ranking', new Module('administrator'), $service);

        $result = $controller->actionIndex();

        $this->assertSame('index', $result['view']);
        $this->assertSame(7, $result['params']['selectedTroteId']);
        $this->assertSame([
            ['universidade_id' => 1, 'nome' => 'Universidade A', 'pontos' => 200, 'participantes' => 2],
        ], $result['params']['universityRanking']);
        $this->assertSame([7 => 'Trote Solidario | 2026.1'], $result['params']['trotes']);
    }

    public function testParticipantRankingActionBuildsViewUsingServiceAndActiveParticipations(): void
    {
        Yii::$app->request->setQueryParams(['trote_id' => 7]);

        $this->insert('trote', ['id' => 7, 'titulo' => 'Trote Solidario', 'edicao' => '2026.1', 'status' => 'ativo']);
        $this->insert('trote', ['id' => 8, 'titulo' => 'Trote Solidario', 'edicao' => '2025.2', 'status' => 'encerrado']);
        $this->insert('universidade', ['id' => 10, 'nome' => 'Universidade A']);
        $this->insert('universidade', ['id' => 11, 'nome' => 'Universidade B']);
        $this->insert('participacao', ['id' => 1, 'user_id' => 99, 'trote_id' => 7, 'universidade_id' => 10, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 2, 'user_id' => 99, 'trote_id' => 7, 'universidade_id' => 11, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 3, 'user_id' => 99, 'trote_id' => 8, 'universidade_id' => 11, 'status' => 'ativo']);

        $service = $this->createMock(RankingCacheServiceInterface::class);
        $service->expects($this->once())
            ->method('findTrotes')
            ->willReturn([
                7 => 'Trote Solidario | 2026.1',
                8 => 'Trote Solidario | 2025.2',
            ]);
        $service->expects($this->once())
            ->method('getUniversityRanking')
            ->with(7)
            ->willReturn([
                ['universidade_id' => 10, 'nome' => 'Universidade A', 'pontos' => 160, 'participantes' => 1],
                ['universidade_id' => 11, 'nome' => 'Universidade B', 'pontos' => 100, 'participantes' => 1],
            ]);

        Yii::$container->set(RankingCacheServiceInterface::class, static fn() => $service);

        $controller = new TestParticipanteDefaultController('default', new Module('participante'));

        $result = $controller->actionRanking();

        $this->assertSame('ranking', $result['view']);
        $this->assertSame(7, $result['params']['selectedTroteId']);
        $this->assertSame([11, 10], $result['params']['userUniversityIds']);
        $this->assertSame('2026.1', $result['params']['troteAtivo']->edicao);
        $this->assertSame([
            ['universidade_id' => 10, 'nome' => 'Universidade A', 'pontos' => 160, 'participantes' => 1],
            ['universidade_id' => 11, 'nome' => 'Universidade B', 'pontos' => 100, 'participantes' => 1],
        ], $result['params']['ranking']);
    }

    private function createParticipantSchema(Connection $db): void
    {
        $db->createCommand('CREATE TABLE trote (id INTEGER PRIMARY KEY, titulo TEXT, edicao TEXT, status TEXT, data_inicio TEXT, data_fim TEXT, descricao TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE universidade (id INTEGER PRIMARY KEY, nome TEXT, cidade TEXT, uf TEXT, ativo INTEGER, link_doacao_alimento TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE participacao (id INTEGER PRIMARY KEY, user_id INTEGER, trote_id INTEGER NOT NULL, universidade_id INTEGER, curso TEXT, status TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE ranking_cache (id INTEGER PRIMARY KEY AUTOINCREMENT, participacao_id INTEGER NOT NULL, trote_id INTEGER NOT NULL, pontuacao_total INTEGER NOT NULL DEFAULT 0, posicao INTEGER, updated_at TEXT NOT NULL)')->execute();
    }

    private function insert(string $table, array $row): void
    {
        Yii::$app->db->createCommand()->insert($table, $row)->execute();
    }
}

class TestAdministratorRankingController extends \app\modules\administrator\controllers\RankingController
{
    public function render($view, $params = [])
    {
        return ['view' => $view, 'params' => $params];
    }
}

class TestParticipanteDefaultController extends \app\modules\participante\controllers\DefaultController
{
    public function render($view, $params = [])
    {
        return ['view' => $view, 'params' => $params];
    }
}

class FakeWebUser extends Component
{
    public int $id;
}
