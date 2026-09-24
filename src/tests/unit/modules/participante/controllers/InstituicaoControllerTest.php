<?php

namespace tests\unit\modules\participante\controllers;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/participante/controllers/InstituicaoController.php';

use app\modules\common\models\Trote;
use app\modules\participante\controllers\InstituicaoController;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\Module;
use yii\db\Connection;
use yii\web\Application;

class InstituicaoControllerTest extends TestCase
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
        Yii::$app->set('db', $db);

        $db->createCommand('CREATE TABLE universidade (id INTEGER PRIMARY KEY, nome TEXT, ativo INTEGER)')->execute();
        $db->createCommand('CREATE TABLE trote (id INTEGER PRIMARY KEY, status TEXT)')->execute();
        $db->createCommand('CREATE TABLE mercado_parceiro (id INTEGER PRIMARY KEY, nome_mercado TEXT)')->execute();
        $db->createCommand('CREATE TABLE mercado_universidade (id INTEGER PRIMARY KEY, mercado_id INTEGER, universidade_id INTEGER, trote_id INTEGER)')->execute();
        $db->createCommand('CREATE TABLE comissao_organizadora (id INTEGER PRIMARY KEY, universidade_id INTEGER, trote_id INTEGER, nome TEXT, ativo INTEGER, ordem INTEGER)')->execute();

        $db->createCommand()->insert('universidade', ['id' => 1, 'nome' => 'Universidade', 'ativo' => 1])->execute();
        $db->createCommand()->batchInsert('trote', ['id', 'status'], [
            [1, Trote::STATUS_ENCERRADO],
            [2, Trote::STATUS_ATIVO],
        ])->execute();
        $db->createCommand()->batchInsert('mercado_parceiro', ['id', 'nome_mercado'], [
            [10, 'Mercado antigo'],
            [20, 'Mercado atual'],
        ])->execute();
        $db->createCommand()->batchInsert('mercado_universidade', ['id', 'mercado_id', 'universidade_id', 'trote_id'], [
            [1, 10, 1, 1],
            [2, 20, 1, 2],
        ])->execute();
        $db->createCommand()->batchInsert(
            'comissao_organizadora',
            ['id', 'universidade_id', 'trote_id', 'nome', 'ativo', 'ordem'],
            [
                [1, 1, 2, 'Pessoa atual', 1, 1],
                [2, 1, 1, 'Pessoa antiga', 1, 1],
            ]
        )->execute();
    }

    protected function tearDown(): void
    {
        Yii::$app->set('db', $this->oldDb);
        parent::tearDown();
    }

    public function testDetalhesFiltraMercadosEComissaoPeloTroteAtivo(): void
    {
        $controller = new TestInstituicaoController('instituicao', new Module('participante'));
        $result = $controller->actionDetalhes(1);

        $this->assertSame('detalhes', $result['view']);
        $this->assertSame(['Mercado atual'], array_column($result['params']['mercadosParceiros'], 'nome_mercado'));
        $this->assertSame(['Pessoa atual'], array_column($result['params']['comissaoOrganizadora'], 'nome'));
    }

    public function testDetalhesNaoExibeComissaoDeTroteEncerrado(): void
    {
        $controller = new TestInstituicaoController('instituicao', new Module('participante'));
        $result = $controller->actionDetalhes(1);

        $this->assertNotContains('Pessoa antiga', array_column($result['params']['comissaoOrganizadora'], 'nome'));
    }
}

class TestInstituicaoController extends InstituicaoController
{
    public function render($view, $params = [])
    {
        return ['view' => $view, 'params' => $params];
    }
}
