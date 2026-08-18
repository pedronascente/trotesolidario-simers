<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/TroteServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/TroteService.php';

use app\modules\common\models\Trote;
use app\modules\common\services\TroteService;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class TroteServiceTest extends TestCase
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
        $db->pdo->sqliteCreateFunction('NOW', static function (): string {
            return date('Y-m-d H:i:s');
        });
        $db->createCommand(
            'CREATE TABLE trote ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT, '
            . 'titulo TEXT NOT NULL, edicao TEXT NOT NULL UNIQUE, descricao TEXT, status TEXT, '
            . 'data_inicio TEXT NOT NULL, data_fim TEXT NOT NULL, created_at TEXT, updated_at TEXT'
            . ')'
        )->execute();
        $db->createCommand(
            'CREATE TABLE participacao ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT, trote_id INTEGER NOT NULL, status TEXT NOT NULL'
            . ')'
        )->execute();
        Yii::$app->set('db', $db);
    }

    protected function tearDown(): void
    {
        Yii::$app->set('db', $this->oldDb);
        parent::tearDown();
    }

    public function testCreateEncerradoSubstituiDataFimFuturaPelaDataAtual(): void
    {
        $trote = $this->novoTrote('2026.1', Trote::STATUS_ENCERRADO);

        $this->assertTrue((new TroteService())->create($trote));
        $this->assertSame(date('Y-m-d'), $trote->data_fim);
    }

    public function testUpdateParaEncerradoSubstituiDataFimFuturaPelaDataAtual(): void
    {
        $trote = $this->novoTrote('2026.1', Trote::STATUS_RASCUNHO);
        $this->assertTrue((new TroteService())->create($trote));

        $trote->status = Trote::STATUS_ENCERRADO;
        Yii::$app->db->createCommand()->insert('participacao', [
            'trote_id' => $trote->id,
            'status' => 'ativo',
        ])->execute();
        $this->assertTrue((new TroteService())->update($trote));
        $this->assertSame(date('Y-m-d'), $trote->data_fim);
        $this->assertSame(
            'encerrado',
            Yii::$app->db->createCommand('SELECT status FROM participacao WHERE trote_id = :trote_id', [
                ':trote_id' => $trote->id,
            ])->queryScalar()
        );
    }

    public function testAtivarTroteEncerraOsDemaisComDataAtual(): void
    {
        $service = new TroteService();
        $anterior = $this->novoTrote('2026.1', Trote::STATUS_ATIVO);
        $atual = $this->novoTrote('2026.2', Trote::STATUS_RASCUNHO);
        $this->assertTrue($service->create($anterior));
        $this->assertTrue($service->create($atual));
        Yii::$app->db->createCommand()->insert('participacao', [
            'trote_id' => $anterior->id,
            'status' => 'ativo',
        ])->execute();

        $this->assertTrue($service->ativar($atual));
        $anterior->refresh();

        $this->assertSame(Trote::STATUS_ENCERRADO, $anterior->status);
        $this->assertSame(date('Y-m-d'), $anterior->data_fim);
        $this->assertSame(
            'encerrado',
            Yii::$app->db->createCommand('SELECT status FROM participacao WHERE trote_id = :trote_id', [
                ':trote_id' => $anterior->id,
            ])->queryScalar()
        );
    }

    private function novoTrote(string $edicao, string $status): Trote
    {
        $trote = new Trote();
        $trote->titulo = 'Trote ' . $edicao;
        $trote->edicao = $edicao;
        $trote->status = $status;
        $trote->data_inicio = '2026-01-01';
        $trote->data_fim = '2026-12-31';

        return $trote;
    }
}
