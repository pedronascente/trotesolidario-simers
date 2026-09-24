<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/MercadoParceiro.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Universidade.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/MercadoUniversidade.php';

use app\modules\common\models\MercadoUniversidade;
use app\modules\common\models\Trote;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class MercadoUniversidadeTest extends TestCase
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
        $db->createCommand('CREATE TABLE trote (id INTEGER PRIMARY KEY, status TEXT)')->execute();
        $db->createCommand('CREATE TABLE mercado_parceiro (id INTEGER PRIMARY KEY)')->execute();
        $db->createCommand('CREATE TABLE universidade (id INTEGER PRIMARY KEY)')->execute();
        $db->createCommand('CREATE TABLE mercado_universidade (id INTEGER PRIMARY KEY, mercado_id INTEGER, universidade_id INTEGER, trote_id INTEGER, created_at INTEGER)')->execute();
        Yii::$app->set('db', $db);

        $db->createCommand()->batchInsert('trote', ['id', 'status'], [
            [1, Trote::STATUS_ENCERRADO],
            [2, Trote::STATUS_ATIVO],
        ])->execute();
        $db->createCommand()->insert('mercado_parceiro', ['id' => 10])->execute();
        $db->createCommand()->insert('universidade', ['id' => 20])->execute();
    }

    protected function tearDown(): void
    {
        Yii::$app->set('db', $this->oldDb);
        parent::tearDown();
    }

    public function testAceitaVinculoComTroteAtivo(): void
    {
        $model = $this->newModel(2);

        $this->assertTrue($model->validate());
    }

    public function testRejeitaVinculoComTroteEncerrado(): void
    {
        $model = $this->newModel(1);

        $this->assertFalse($model->validate());
        $this->assertSame('Selecione um trote ativo.', $model->getFirstError('trote_id'));
    }

    public function testRejeitaCombinacaoDuplicadaNoMesmoTrote(): void
    {
        Yii::$app->db->createCommand()->insert('mercado_universidade', [
            'id' => 1,
            'mercado_id' => 10,
            'universidade_id' => 20,
            'trote_id' => 2,
        ])->execute();

        $model = $this->newModel(2);

        $this->assertFalse($model->validate());
        $this->assertContains(
            'Este mercado já está vinculado a esta universidade no trote selecionado.',
            array_merge(...array_values($model->getErrors()))
        );
    }

    private function newModel(int $troteId): MercadoUniversidade
    {
        $model = new MercadoUniversidade();
        $model->trote_id = $troteId;
        $model->mercado_id = 10;
        $model->universidade_id = 20;

        return $model;
    }
}
