<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Doacao.php';

use app\modules\common\models\Doacao;
use app\modules\common\models\Trote;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class DoacaoTroteStatusTest extends TestCase
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
            'CREATE TABLE trote (id INTEGER PRIMARY KEY, status TEXT)'
        )->execute();
        $db->createCommand(
            'CREATE TABLE participacao (id INTEGER PRIMARY KEY, trote_id INTEGER, status TEXT)'
        )->execute();
        $db->createCommand(
            'CREATE TABLE doacao ('
            . 'id INTEGER PRIMARY KEY, participacao_id INTEGER, tipo_doacao_id INTEGER, evento_id INTEGER, '
            . 'arquivo TEXT, status TEXT, motivo_reprovado TEXT, validado_por INTEGER, validado_em TEXT, '
            . 'cpf_snapshot TEXT, edicao_snapshot TEXT, created_at TEXT, updated_at TEXT'
            . ')'
        )->execute();
        Yii::$app->set('db', $db);
    }

    protected function tearDown(): void
    {
        Yii::$app->set('db', $this->oldDb);
        parent::tearDown();
    }

    public function testNovaDoacaoERecusadaMesmoSeParticipacaoLegadaAindaEstiverAtiva(): void
    {
        $this->insertParticipacao(1, Trote::STATUS_ENCERRADO);
        $doacao = new Doacao();
        $doacao->participacao_id = 1;

        $this->assertFalse($doacao->validate(['participacao_id']));
        $this->assertSame(
            'Nao e possivel registrar doacoes para um trote encerrado.',
            $doacao->getFirstError('participacao_id')
        );
    }

    public function testNovaDoacaoAceitaParticipacaoAtivaDeTroteAtivo(): void
    {
        $this->insertParticipacao(2, Trote::STATUS_ATIVO);
        $doacao = new Doacao();
        $doacao->participacao_id = 2;

        $this->assertTrue($doacao->validate(['participacao_id']));
    }

    private function insertParticipacao(int $id, string $troteStatus): void
    {
        Yii::$app->db->createCommand()->insert('trote', [
            'id' => $id,
            'status' => $troteStatus,
        ])->execute();
        Yii::$app->db->createCommand()->insert('participacao', [
            'id' => $id,
            'trote_id' => $id,
            'status' => 'ativo',
        ])->execute();
    }
}
