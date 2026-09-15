<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Trote.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Universidade.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/TipoCategoriaCusto.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/CategoriaCusto.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/DistribuicaoCusto.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/GestaoCustosServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/GestaoCustosService.php';

use app\modules\common\models\CategoriaCusto;
use app\modules\common\models\DistribuicaoCusto;
use app\modules\common\models\Trote;
use app\modules\common\models\TipoCategoriaCusto;
use app\modules\common\services\GestaoCustosService;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class GestaoCustosServiceTest extends TestCase
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
        $db->pdo->sqliteCreateFunction('NOW', static fn() => date('Y-m-d H:i:s'));
        foreach ([
            'CREATE TABLE trote (id INTEGER PRIMARY KEY, titulo TEXT, edicao TEXT UNIQUE, descricao TEXT, status TEXT, data_inicio TEXT, data_fim TEXT, created_at TEXT, updated_at TEXT)',
            'CREATE TABLE universidade (id INTEGER PRIMARY KEY, nome TEXT, cidade TEXT, uf TEXT, icon TEXT, link_doacao_alimento TEXT, ativo INTEGER, created_at TEXT, updated_at TEXT)',
            'CREATE TABLE tipo_categoria_custo (id INTEGER PRIMARY KEY AUTOINCREMENT, nome TEXT UNIQUE NOT NULL, descricao TEXT, ativo INTEGER NOT NULL, created_at TEXT, updated_at TEXT)',
            'CREATE TABLE categoria_custo (id INTEGER PRIMARY KEY AUTOINCREMENT, trote_id INTEGER NOT NULL, tipo_categoria_custo_id INTEGER NOT NULL, valor_previsto NUMERIC NOT NULL, descricao TEXT NOT NULL, observacao TEXT, created_at TEXT, updated_at TEXT)',
            'CREATE UNIQUE INDEX uq_categoria ON categoria_custo (trote_id, tipo_categoria_custo_id)',
            'CREATE TABLE distribuicao_custo (id INTEGER PRIMARY KEY AUTOINCREMENT, categoria_custo_id INTEGER NOT NULL, universidade_id INTEGER NOT NULL, valor_previsto NUMERIC NOT NULL, observacao TEXT, created_at TEXT, updated_at TEXT)',
            'CREATE UNIQUE INDEX uq_distribuicao ON distribuicao_custo (categoria_custo_id, universidade_id)',
        ] as $sql) {
            $db->createCommand($sql)->execute();
        }
        Yii::$app->set('db', $db);
        Yii::$app->db->createCommand()->batchInsert('tipo_categoria_custo', ['id', 'nome', 'ativo'], [
            [1, 'Logistica', 1], [2, 'Alimentacao', 1],
        ])->execute();
        $this->insertTrote(1, Trote::STATUS_ATIVO);
        $this->insertTrote(2, Trote::STATUS_ENCERRADO);
        Yii::$app->db->createCommand()->batchInsert('universidade', ['id', 'nome', 'cidade', 'uf', 'ativo'], [
            [1, 'Universidade A', 'Pelotas', 'RS', 1],
            [2, 'Universidade B', 'Rio Grande', 'RS', 1],
        ])->execute();
    }

    protected function tearDown(): void
    {
        Yii::$app->set('db', $this->oldDb);
        parent::tearDown();
    }

    public function testSalvaCategoriaEDistribuicoesEmConjunto(): void
    {
        $categoria = $this->novaCategoria(1, '1.000,00');
        $resultado = (new GestaoCustosService())->save($categoria, [
            ['universidade_id' => 1, 'valor_previsto' => '300,00', 'observacao' => 'Transporte'],
            ['universidade_id' => 2, 'valor_previsto' => '200,00', 'observacao' => 'Alimentacao'],
        ]);

        $this->assertTrue($resultado);
        $this->assertSame(1, (int) CategoriaCusto::find()->count());
        $this->assertSame(2, (int) DistribuicaoCusto::find()->count());
    }

    public function testBloqueiaDistribuicaoAcimaDoValorPrevisto(): void
    {
        $categoria = $this->novaCategoria(1, '100,00');
        $resultado = (new GestaoCustosService())->save($categoria, [
            ['universidade_id' => 1, 'valor_previsto' => '100,01'],
        ]);

        $this->assertFalse($resultado);
        $this->assertSame('O total distribuido nao pode ultrapassar o valor previsto.', $categoria->getFirstError('valor_previsto'));
        $this->assertSame(0, (int) CategoriaCusto::find()->count());
    }

    public function testBloqueiaUniversidadeDuplicada(): void
    {
        $categoria = $this->novaCategoria(1, '500,00');
        $resultado = (new GestaoCustosService())->save($categoria, [
            ['universidade_id' => 1, 'valor_previsto' => '100,00'],
            ['universidade_id' => 1, 'valor_previsto' => '50,00'],
        ]);

        $this->assertFalse($resultado);
        $this->assertSame(0, (int) CategoriaCusto::find()->count());
    }

    public function testBloqueiaCustoEmTroteEncerrado(): void
    {
        $categoria = $this->novaCategoria(2, '100,00');

        $this->assertFalse((new GestaoCustosService())->save($categoria, []));
        $this->assertSame('Nao e possivel alterar custos de um trote encerrado.', $categoria->getFirstError('trote_id'));
    }

    public function testEditaCategoriaPreservandoDistribuicaoEAdicionandoOutra(): void
    {
        $service = new GestaoCustosService();
        $categoria = $this->novaCategoria(1, '1.000,00');
        $this->assertTrue($service->save($categoria, [
            ['universidade_id' => 1, 'valor_previsto' => '300,00'],
        ]));
        $existente = DistribuicaoCusto::findOne(['categoria_custo_id' => $categoria->id, 'universidade_id' => 1]);

        $this->assertTrue($service->save($categoria, [
            ['id' => $existente->id, 'universidade_id' => 1, 'valor_previsto' => '350,00'],
            ['universidade_id' => 2, 'valor_previsto' => '200,00'],
        ]));

        $this->assertSame(2, (int) DistribuicaoCusto::find()->where(['categoria_custo_id' => $categoria->id])->count());
        $this->assertSame('350', (string) DistribuicaoCusto::findOne($existente->id)->valor_previsto);
    }

    public function testRemoveSomenteDistribuicaoAusenteDoFormulario(): void
    {
        $service = new GestaoCustosService();
        $categoria = $this->novaCategoria(1, '1.000,00');
        $this->assertTrue($service->save($categoria, [
            ['universidade_id' => 1, 'valor_previsto' => '300,00'],
            ['universidade_id' => 2, 'valor_previsto' => '200,00'],
        ]));
        $mantida = DistribuicaoCusto::findOne(['categoria_custo_id' => $categoria->id, 'universidade_id' => 1]);

        $this->assertTrue($service->save($categoria, [
            ['id' => $mantida->id, 'universidade_id' => 1, 'valor_previsto' => '300,00'],
        ]));

        $this->assertSame(1, (int) DistribuicaoCusto::find()->where(['categoria_custo_id' => $categoria->id])->count());
        $this->assertNotNull(DistribuicaoCusto::findOne($mantida->id));
    }

    public function testBloqueiaIdDeDistribuicaoPertencenteAOutraCategoria(): void
    {
        $service = new GestaoCustosService();
        $categoriaA = $this->novaCategoria(1, '500,00');
        $this->assertTrue($service->save($categoriaA, [['universidade_id' => 1, 'valor_previsto' => '100,00']]));
        $distribuicaoA = DistribuicaoCusto::findOne(['categoria_custo_id' => $categoriaA->id]);

        $categoriaB = $this->novaCategoria(1, '500,00');
        $categoriaB->tipo_categoria_custo_id = 2;
        $this->assertTrue($service->save($categoriaB, []));

        $this->assertFalse($service->save($categoriaB, [
            ['id' => $distribuicaoA->id, 'universidade_id' => 1, 'valor_previsto' => '100,00'],
        ]));
        $this->assertSame('Uma distribuicao informada nao pertence a esta categoria.', $categoriaB->getFirstError('valor_previsto'));
    }

    public function testPermiteTrocarUniversidadesEntreDistribuicoesExistentes(): void
    {
        $service = new GestaoCustosService();
        $categoria = $this->novaCategoria(1, '1.000,00');
        $this->assertTrue($service->save($categoria, [
            ['universidade_id' => 1, 'valor_previsto' => '300,00'],
            ['universidade_id' => 2, 'valor_previsto' => '200,00'],
        ]));
        $primeira = DistribuicaoCusto::findOne(['categoria_custo_id' => $categoria->id, 'universidade_id' => 1]);
        $segunda = DistribuicaoCusto::findOne(['categoria_custo_id' => $categoria->id, 'universidade_id' => 2]);

        $this->assertTrue($service->save($categoria, [
            ['id' => $primeira->id, 'universidade_id' => 2, 'valor_previsto' => '300,00'],
            ['id' => $segunda->id, 'universidade_id' => 1, 'valor_previsto' => '200,00'],
        ]));
        $this->assertSame(2, (int) DistribuicaoCusto::find()->where(['categoria_custo_id' => $categoria->id])->count());
    }

    public function testNaoPermiteTransferirCustoEntreTrotes(): void
    {
        Yii::$app->db->createCommand()->insert('categoria_custo', [
            'trote_id' => 2,
            'tipo_categoria_custo_id' => 1,
            'valor_previsto' => 500,
            'descricao' => 'Custo encerrado',
        ])->execute();
        $categoria = CategoriaCusto::findOne(['trote_id' => 2]);
        $categoria->trote_id = 1;

        $this->assertFalse((new GestaoCustosService())->save($categoria, []));
        $this->assertSame('Nao e permitido transferir um custo para outro trote.', $categoria->getFirstError('trote_id'));
        $this->assertSame(2, (int) CategoriaCusto::findOne($categoria->id)->trote_id);
    }

    public function testRejeitaValorTotalComTextoParcialmenteNumerico(): void
    {
        $categoria = $this->novaCategoria(1, '10abc');

        $this->assertFalse((new GestaoCustosService())->save($categoria, []));
        $this->assertSame('Informe um valor monetario valido.', $categoria->getFirstError('valor_previsto'));
    }

    public function testRejeitaValorDeDistribuicaoMalformado(): void
    {
        $categoria = $this->novaCategoria(1, '500,00');

        $this->assertFalse((new GestaoCustosService())->save($categoria, [
            ['universidade_id' => 1, 'valor_previsto' => '1,2,3'],
        ]));
        $this->assertSame('Distribuicao: informe um valor monetario valido.', $categoria->getFirstError('valor_previsto'));
    }

    public function testCategoriaInativaSoApareceQuandoJaEstaVinculada(): void
    {
        Yii::$app->db->createCommand()->update('tipo_categoria_custo', ['ativo' => 0], ['id' => 2])->execute();
        $service = new GestaoCustosService();

        $this->assertArrayNotHasKey(2, $service->getTiposCategoria());
        $this->assertArrayHasKey(2, $service->getTiposCategoria([2]));
    }

    public function testCadastroMestreBloqueiaNomeDuplicado(): void
    {
        $tipo = new TipoCategoriaCusto(['nome' => 'Logistica', 'ativo' => 1]);

        $this->assertFalse($tipo->validate());
        $this->assertSame('Ja existe uma categoria com este nome.', $tipo->getFirstError('nome'));
    }

    private function novaCategoria(int $troteId, string $valor): CategoriaCusto
    {
        $model = new CategoriaCusto();
        $model->trote_id = $troteId;
        $model->tipo_categoria_custo_id = 1;
        $model->valor_previsto = $valor;
        $model->descricao = 'Custos de logistica';
        return $model;
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
