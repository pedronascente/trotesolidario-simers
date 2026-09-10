<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Banner.php';

use app\modules\common\models\Banner;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\web\Application;

class BannerTest extends TestCase
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
            'CREATE TABLE banner ('
            . 'id INTEGER PRIMARY KEY AUTOINCREMENT, tipo TEXT, img_dsk TEXT, img_mob TEXT, '
            . 'ativo INTEGER, created_at TEXT, updated_at TEXT'
            . ')'
        )->execute();
        Yii::$app->set('db', $db);
    }

    protected function tearDown(): void
    {
        Yii::$app->set('db', $this->oldDb);
        parent::tearDown();
    }

    public function testRejeitaPosicaoDesconhecida(): void
    {
        $banner = new Banner();
        $banner->tipo = 'Posição inexistente';
        $banner->ativo = 1;

        $this->assertFalse($banner->validate());
        $this->assertTrue($banner->hasErrors('tipo'));
    }

    public function testRejeitaInformativoComoPosicaoDeBanner(): void
    {
        $banner = new Banner();
        $banner->tipo = 'Informativo';
        $banner->ativo = 1;

        $this->assertFalse($banner->validate());
        $this->assertTrue($banner->hasErrors('tipo'));
    }

    public function testNovoBannerExigeAoMenosUmaImagem(): void
    {
        $banner = new Banner();
        $banner->tipo = Banner::TIPO_HOME;
        $banner->ativo = 1;

        $this->assertFalse($banner->validate());
        $this->assertTrue($banner->hasErrors('file_dsk'));
    }

    public function testRejeitaSegundoBannerDoMesmoTipo(): void
    {
        Yii::$app->db->createCommand()->insert('banner', [
            'tipo' => Banner::TIPO_LOGIN,
            'ativo' => 1,
            'img_dsk' => 'banner-existente.png',
        ])->execute();

        $banner = new Banner();
        $banner->tipo = Banner::TIPO_LOGIN;
        $banner->ativo = 1;

        $this->assertFalse($banner->validate());
        $this->assertSame(
            'Já existe um banner cadastrado para este local de exibição.',
            $banner->getFirstError('tipo')
        );
    }

    public function testCamposPersistidosDeImagemNaoSaoCarregadosDoFormulario(): void
    {
        $banner = new Banner();
        $banner->load([
            'Banner' => [
                'tipo' => Banner::TIPO_HOME,
                'ativo' => 1,
                'img_dsk' => '../../arquivo-forjado.png',
            ],
        ]);

        $this->assertNull($banner->img_dsk);
    }
}
