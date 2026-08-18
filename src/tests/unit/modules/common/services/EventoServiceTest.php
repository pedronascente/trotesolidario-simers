<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Evento.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/EventoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/EventoService.php';

use app\modules\common\models\Evento;
use app\modules\common\models\Trote;
use app\modules\common\services\EventoService;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class EventoServiceTest extends TestCase
{
    private $oldDb;

    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }

        $this->oldDb = Yii::$app->db;
        $db = new Connection(['dsn' => 'sqlite::memory:']);
        $db->open();
        $db->createCommand(
            'CREATE TABLE trote ('
            . 'id INTEGER PRIMARY KEY, titulo TEXT, edicao TEXT, descricao TEXT, status TEXT, '
            . 'data_inicio TEXT, data_fim TEXT, created_at TEXT, updated_at TEXT'
            . ')'
        )->execute();
        $db->createCommand(
            'CREATE TABLE evento ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT, trote_id INTEGER NOT NULL, '
            . 'nome TEXT NOT NULL, data_evento TEXT NOT NULL'
            . ')'
        )->execute();
        Yii::$app->set('db', $db);

        $this->insertTrote(1, Trote::STATUS_ENCERRADO);
        $this->insertTrote(2, Trote::STATUS_ATIVO);
    }

    protected function tearDown(): void
    {
        Yii::$app->set('db', $this->oldDb);
        parent::tearDown();
    }

    public function testCreateBloqueiaEventoParaTroteEncerrado(): void
    {
        $evento = $this->novoEvento(1);

        $this->assertFalse((new EventoService())->create($evento));
        $this->assertSame(
            'Nao e possivel criar eventos para um trote encerrado.',
            $evento->getFirstError('trote_id')
        );
        $this->assertSame(0, (int) Evento::find()->count());
    }

    public function testCreatePermiteEventoParaTroteAtivo(): void
    {
        $evento = $this->novoEvento(2);

        $this->assertTrue((new EventoService())->create($evento));
        $this->assertSame(1, (int) Evento::find()->count());
    }

    public function testFindTrotesParaCriacaoExcluiEncerrados(): void
    {
        $trotes = (new EventoService())->findTrotes(false);

        $this->assertArrayNotHasKey(1, $trotes);
        $this->assertArrayHasKey(2, $trotes);
    }

    private function novoEvento(int $troteId): Evento
    {
        $evento = new Evento();
        $evento->trote_id = $troteId;
        $evento->nome = 'Evento de teste';
        $evento->data_evento = '2026-08-18T10:00';

        return $evento;
    }

    private function insertTrote(int $id, string $status): void
    {
        Yii::$app->db->createCommand()->insert('trote', [
            'id' => $id,
            'titulo' => 'Trote ' . $id,
            'edicao' => '2026.' . $id,
            'status' => $status,
            'data_inicio' => '2026-01-01',
            'data_fim' => '2026-12-31',
        ])->execute();
    }
}
