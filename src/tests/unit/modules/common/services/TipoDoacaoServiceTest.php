<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 5) . '/modules/common/models/TipoDoacao.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/RankingCacheServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/contracts/TipoDoacaoServiceInterface.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/TipoDoacaoService.php';

use app\modules\common\models\TipoDoacao;
use app\modules\common\services\TipoDoacaoService;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class TipoDoacaoServiceTest extends TestCase
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

    public function testCreateDoesNotTriggerRankingRebuild(): void
    {
        $this->mockTransactionalDb();

        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->never())->method('rebuild');

        $service = new TipoDoacaoService($rankingCacheService);
        $model = new FakeTipoDoacao();
        $model->validateResult = true;
        $model->saveResult = true;

        $this->assertTrue($service->create($model));
        $this->assertSame(1, $model->saveCalls);
    }

    public function testUpdateTriggersRankingRebuildWhenPontuacaoChanges(): void
    {
        $this->mockTransactionalDb();

        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->once())->method('rebuild')->with(null);

        $service = new TipoDoacaoService($rankingCacheService);
        $model = new FakeTipoDoacao();
        $model->pontuacao_ranking = 150;
        $model->oldAttributesMap = ['pontuacao_ranking' => 100];
        $model->validateResult = true;
        $model->saveResult = true;

        $this->assertTrue($service->update($model));
        $this->assertSame(1, $model->saveCalls);
    }

    public function testUpdateDoesNotTriggerRankingRebuildWhenPontuacaoIsUnchanged(): void
    {
        $this->mockTransactionalDb();

        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->never())->method('rebuild');

        $service = new TipoDoacaoService($rankingCacheService);
        $model = new FakeTipoDoacao();
        $model->pontuacao_ranking = 100;
        $model->oldAttributesMap = ['pontuacao_ranking' => 100];
        $model->validateResult = true;
        $model->saveResult = true;

        $this->assertTrue($service->update($model));
        $this->assertSame(1, $model->saveCalls);
    }

    public function testDeleteTriggersRankingRebuildAfterSuccessfulDeletion(): void
    {
        $this->mockTransactionalDb();

        $rankingCacheService = $this->createMock(RankingCacheServiceInterface::class);
        $rankingCacheService->expects($this->once())->method('rebuild')->with(null);

        $service = new TipoDoacaoService($rankingCacheService);
        $model = new FakeTipoDoacao();
        $model->deleteResult = 1;

        $this->assertTrue($service->delete($model));
        $this->assertSame(1, $model->deleteCalls);
    }

    private function mockTransactionalDb(): void
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
    }
}

class FakeTipoDoacao extends TipoDoacao
{
    public $pontuacao_ranking;
    public $validateResult = true;
    public $saveResult = true;
    public $saveCalls = 0;
    public $deleteResult = 1;
    public $deleteCalls = 0;
    public $oldAttributesMap = [];

    public function attributes(): array
    {
        return ['id', 'nome', 'descricao', 'carga_horaria', 'pontuacao_ranking', 'ativo'];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->validateResult;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $this->saveCalls++;
        return $this->saveResult;
    }

    public function delete()
    {
        $this->deleteCalls++;
        return $this->deleteResult;
    }

    public function getOldAttribute($name)
    {
        return $this->oldAttributesMap[$name] ?? null;
    }
}
