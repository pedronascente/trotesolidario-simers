<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Doacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/RankingCache.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/RankingCacheServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/RankingCacheService.php';

use app\modules\common\services\RankingCacheService;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class RankingCacheServiceTest extends TestCase
{
    private $oldDb;

    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }

        $this->oldDb = Yii::$app->db;
        $db = new Connection([
            'dsn' => 'sqlite::memory:',
        ]);
        $db->open();
        $db->pdo->sqliteCreateFunction('NOW', static fn() => date('Y-m-d H:i:s'));

        Yii::$app->set('db', $db);
        $this->createSchema($db);
    }

    protected function tearDown(): void
    {
        if ($this->oldDb !== null) {
            Yii::$app->set('db', $this->oldDb);
            $this->oldDb = null;
        }

        parent::tearDown();
    }

    public function testRebuildCalculatesScoresAndCompetitionPositions(): void
    {
        $this->insert('trote', ['id' => 1, 'titulo' => 'Trote Solidario', 'edicao' => '2026.1', 'status' => 'ativo']);

        $this->insert('universidade', ['id' => 1, 'nome' => 'Universidade A']);
        $this->insert('universidade', ['id' => 2, 'nome' => 'Universidade B']);

        $this->insert('tipo_doacao', ['id' => 1, 'nome' => 'Alimento', 'pontuacao_ranking' => 100]);
        $this->insert('tipo_doacao', ['id' => 2, 'nome' => 'Sangue', 'pontuacao_ranking' => 60]);

        $this->insert('participacao', ['id' => 1, 'trote_id' => 1, 'universidade_id' => 1, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 2, 'trote_id' => 1, 'universidade_id' => 2, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 3, 'trote_id' => 1, 'universidade_id' => 1, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 4, 'trote_id' => 1, 'universidade_id' => 2, 'status' => 'cancelado']);
        $this->insert('participacao', ['id' => 5, 'trote_id' => 1, 'universidade_id' => 2, 'status' => 'ativo']);

        $this->insert('doacao', ['id' => 1, 'participacao_id' => 1, 'tipo_doacao_id' => 1, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 2, 'participacao_id' => 1, 'tipo_doacao_id' => 2, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 3, 'participacao_id' => 2, 'tipo_doacao_id' => 1, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 4, 'participacao_id' => 2, 'tipo_doacao_id' => 2, 'status' => 'pendente']);
        $this->insert('doacao', ['id' => 5, 'participacao_id' => 3, 'tipo_doacao_id' => 1, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 6, 'participacao_id' => 4, 'tipo_doacao_id' => 1, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 7, 'participacao_id' => 5, 'tipo_doacao_id' => 2, 'status' => 'aprovada']);

        $service = new RankingCacheService();

        $this->assertSame(4, $service->rebuild(1));

        $rows = Yii::$app->db->createCommand(
            'SELECT participacao_id, pontuacao_total, posicao FROM ranking_cache WHERE trote_id = 1 ORDER BY pontuacao_total DESC, participacao_id ASC'
        )->queryAll();

        $this->assertSame([
            ['participacao_id' => 1, 'pontuacao_total' => 160, 'posicao' => 1],
            ['participacao_id' => 2, 'pontuacao_total' => 100, 'posicao' => 2],
            ['participacao_id' => 3, 'pontuacao_total' => 100, 'posicao' => 2],
            ['participacao_id' => 5, 'pontuacao_total' => 60, 'posicao' => 4],
        ], array_map([$this, 'normalizeRankingRow'], $rows));
    }

    public function testGetUniversityRankingAggregatesPointsParticipantsAndOrdering(): void
    {
        $this->insert('trote', ['id' => 1, 'titulo' => 'Trote Solidario', 'edicao' => '2026.1', 'status' => 'ativo']);

        $this->insert('universidade', ['id' => 1, 'nome' => 'Alpha']);
        $this->insert('universidade', ['id' => 2, 'nome' => 'Beta']);
        $this->insert('universidade', ['id' => 3, 'nome' => 'Gamma']);

        $this->insert('tipo_doacao', ['id' => 1, 'nome' => 'Alimento', 'pontuacao_ranking' => 100]);
        $this->insert('tipo_doacao', ['id' => 2, 'nome' => 'Sangue', 'pontuacao_ranking' => 60]);

        $this->insert('participacao', ['id' => 1, 'trote_id' => 1, 'universidade_id' => 1, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 2, 'trote_id' => 1, 'universidade_id' => 1, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 3, 'trote_id' => 1, 'universidade_id' => 2, 'status' => 'ativo']);
        $this->insert('participacao', ['id' => 4, 'trote_id' => 1, 'universidade_id' => 3, 'status' => 'ativo']);

        $this->insert('doacao', ['id' => 1, 'participacao_id' => 1, 'tipo_doacao_id' => 1, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 2, 'participacao_id' => 3, 'tipo_doacao_id' => 1, 'status' => 'aprovada']);
        $this->insert('doacao', ['id' => 3, 'participacao_id' => 4, 'tipo_doacao_id' => 2, 'status' => 'aprovada']);

        $service = new RankingCacheService();
        $service->rebuild(1);

        $ranking = $service->getUniversityRanking(1);

        $this->assertSame([
            [
                'universidade_id' => 1,
                'nome' => 'Alpha',
                'trote_id' => 1,
                'pontos' => 100,
                'participantes' => 2,
                'melhor_posicao' => 1,
            ],
            [
                'universidade_id' => 2,
                'nome' => 'Beta',
                'trote_id' => 1,
                'pontos' => 100,
                'participantes' => 1,
                'melhor_posicao' => 1,
            ],
            [
                'universidade_id' => 3,
                'nome' => 'Gamma',
                'trote_id' => 1,
                'pontos' => 60,
                'participantes' => 1,
                'melhor_posicao' => 3,
            ],
        ], array_map([$this, 'normalizeUniversityRow'], $ranking));
    }

    private function createSchema(Connection $db): void
    {
        $db->createCommand('CREATE TABLE trote (id INTEGER PRIMARY KEY, titulo TEXT, edicao TEXT, status TEXT, data_inicio TEXT, data_fim TEXT)')->execute();
        $db->createCommand('CREATE TABLE universidade (id INTEGER PRIMARY KEY, nome TEXT)')->execute();
        $db->createCommand('CREATE TABLE tipo_doacao (id INTEGER PRIMARY KEY, nome TEXT, pontuacao_ranking INTEGER NOT NULL DEFAULT 0)')->execute();
        $db->createCommand('CREATE TABLE participacao (id INTEGER PRIMARY KEY, user_id INTEGER, trote_id INTEGER NOT NULL, universidade_id INTEGER, curso TEXT, status TEXT, created_at TEXT, updated_at TEXT)')->execute();
        $db->createCommand('CREATE TABLE doacao (id INTEGER PRIMARY KEY, participacao_id INTEGER NOT NULL, tipo_doacao_id INTEGER NOT NULL, status TEXT NOT NULL)')->execute();
        $db->createCommand('CREATE TABLE ranking_cache (id INTEGER PRIMARY KEY AUTOINCREMENT, participacao_id INTEGER NOT NULL, trote_id INTEGER NOT NULL, pontuacao_total INTEGER NOT NULL DEFAULT 0, posicao INTEGER, updated_at TEXT NOT NULL)')->execute();
    }

    private function insert(string $table, array $row): void
    {
        Yii::$app->db->createCommand()->insert($table, $row)->execute();
    }

    private function normalizeRankingRow(array $row): array
    {
        return [
            'participacao_id' => (int) $row['participacao_id'],
            'pontuacao_total' => (int) $row['pontuacao_total'],
            'posicao' => (int) $row['posicao'],
        ];
    }

    private function normalizeUniversityRow(array $row): array
    {
        return [
            'universidade_id' => (int) $row['universidade_id'],
            'nome' => $row['nome'],
            'trote_id' => (int) $row['trote_id'],
            'pontos' => (int) $row['pontos'],
            'participantes' => (int) $row['participantes'],
            'melhor_posicao' => (int) $row['melhor_posicao'],
        ];
    }
}
