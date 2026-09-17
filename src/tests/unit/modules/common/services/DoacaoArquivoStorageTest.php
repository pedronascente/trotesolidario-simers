<?php

namespace tests\unit\modules\common\services;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/services/DoacaoArquivoStorage.php';

use app\modules\common\services\DoacaoArquivoStorage;
use PHPUnit\Framework\TestCase;
use Yii;

class DoacaoArquivoStorageTest extends TestCase
{
    private string $directory;
    private string $originalAlias;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalAlias = Yii::getAlias('@imgArquivosDoacao');
        $this->directory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'doacao-storage-' . uniqid('', true);
        mkdir($this->directory, 0775, true);
        Yii::setAlias('@imgArquivosDoacao', $this->directory);
    }

    protected function tearDown(): void
    {
        Yii::setAlias('@imgArquivosDoacao', $this->originalAlias);

        foreach (glob($this->directory . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        if (is_dir($this->directory)) {
            rmdir($this->directory);
        }

        parent::tearDown();
    }

    public function testResolveRejeitaNomeComCaminho(): void
    {
        $this->assertNull(DoacaoArquivoStorage::resolve('../comprovante.jpg'));
        $this->assertNull(DoacaoArquivoStorage::resolve('pasta/comprovante.jpg'));
    }

    public function testResolveLocalizaArquivoNoDiretorioProtegido(): void
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . 'comprovante.txt';
        file_put_contents($path, 'comprovante');

        $this->assertSame($path, DoacaoArquivoStorage::resolve('comprovante.txt'));
    }

    public function testIsImageDistingueImagemDeArquivoComum(): void
    {
        $image = $this->directory . DIRECTORY_SEPARATOR . 'comprovante.png';
        $text = $this->directory . DIRECTORY_SEPARATOR . 'comprovante.txt';
        file_put_contents(
            $image,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
        );
        file_put_contents($text, 'nao e uma imagem');

        $this->assertTrue(DoacaoArquivoStorage::isImage($image));
        $this->assertFalse(DoacaoArquivoStorage::isImage($text));
    }

    public function testRemoveExcluiArquivoResolvido(): void
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . 'comprovante.txt';
        file_put_contents($path, 'comprovante');

        $this->assertTrue(DoacaoArquivoStorage::remove('comprovante.txt'));
        $this->assertFileDoesNotExist($path);
    }
}
