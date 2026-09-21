<?php

namespace tests\unit\modules\administrator\views;

use PHPUnit\Framework\TestCase;

class DoacaoIndexViewTest extends TestCase
{
    public function testRejectionFlowUsesStableSubmitAndPjaxTargets(): void
    {
        $view = file_get_contents(
            dirname(__DIR__, 5) . '/modules/administrator/views/doacao/index.php'
        );

        $this->assertIsString($view);
        $this->assertStringContainsString("'id' => 'doacao-grid'", $view);
        $this->assertStringContainsString("'options' => ['id' => 'doacao-grid-pjax']", $view);
        $this->assertStringContainsString('type="submit" class="btn btn-danger"', $view);
        $this->assertStringContainsString("container: '#doacao-grid-pjax'", $view);
        $this->assertStringNotContainsString("container: '#w0-pjax'", $view);
        $this->assertStringContainsString(".on('click.doacaoRejeicao', '.btn-rejeitar'", $view);
        $this->assertStringContainsString(".off('submit.doacaoRejeicao')", $view);
        $this->assertStringContainsString('$.post($reprovarUrl, requestData)', $view);
        $this->assertStringNotContainsString("$.post('<?=", $view);
    }

    public function testGalleryUsesLocalBootstrapModalInsteadOfExternalFancybox(): void
    {
        $view = file_get_contents(
            dirname(__DIR__, 5) . '/modules/administrator/views/doacao/index.php'
        );

        $this->assertIsString($view);
        $this->assertStringContainsString('id="modalGaleriaDoacao"', $view);
        $this->assertStringContainsString(".on('click.doacaoGaleria', '.doacao-thumb-link'", $view);
        $this->assertStringContainsString('id="galeriaAnterior"', $view);
        $this->assertStringContainsString('id="galeriaProxima"', $view);
        $this->assertStringContainsString('id="miniaturasGaleriaDoacao"', $view);
        $this->assertStringContainsString('id="contadorGaleriaDoacao"', $view);
        $this->assertStringContainsString("event.key === 'ArrowLeft'", $view);
        $this->assertStringContainsString("event.key === 'ArrowRight'", $view);
        $this->assertStringNotContainsString('FancyboxAsset', $view);
        $this->assertStringNotContainsString('data-fancybox', $view);
    }
}
