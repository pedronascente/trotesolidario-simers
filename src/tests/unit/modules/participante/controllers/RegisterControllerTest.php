<?php

namespace tests\unit\modules\participante\controllers;

require_once dirname(__DIR__, 5) . '/modules/participante/controllers/RegisterController.php';

use app\modules\participante\controllers\RegisterController;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\web\Application;

class RegisterControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }
    }

    protected function tearDown(): void
    {
        $_GET = [];

        parent::tearDown();
    }

    public function testActionIndexPrefillsCpfFromQueryString(): void
    {
        $_GET['cpf'] = '52998224725';

        $controller = new TestRegisterController('register', Yii::$app);

        $result = $controller->actionIndex();

        $this->assertSame('rendered', $result);
        $this->assertSame('index', $controller->capturedView);
        $this->assertSame('529.982.247-25', $controller->capturedParams['model']->cpf);
    }

    public function testActionIndexKeepsCpfEmptyWhenQueryStringIsMissing(): void
    {
        $controller = new TestRegisterController('register', Yii::$app);

        $result = $controller->actionIndex();

        $this->assertSame('rendered', $result);
        $this->assertNull($controller->capturedParams['model']->cpf);
    }

    public function testActionIndexAcceptsMaskedCpfFromQueryString(): void
    {
        $_GET['cpf'] = '529.982.247-25';

        $controller = new TestRegisterController('register', Yii::$app);

        $result = $controller->actionIndex();

        $this->assertSame('rendered', $result);
        $this->assertSame('529.982.247-25', $controller->capturedParams['model']->cpf);
    }
}

class TestRegisterController extends RegisterController
{
    public string $capturedView = '';
    public array $capturedParams = [];

    public function render($view, $params = [])
    {
        $this->capturedView = $view;
        $this->capturedParams = $params;

        return 'rendered';
    }
}
