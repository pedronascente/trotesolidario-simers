<?php

namespace tests\unit\modules\participante\controllers;

require_once dirname(__DIR__, 5) . '/modules/participante/controllers/DefaultController.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/ParticipantStartParticipationForm.php';

use app\modules\common\models\ParticipantStartParticipationForm;
use app\modules\participante\controllers\DefaultController;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\Component;
use yii\web\Application;
use yii\web\Request;
use yii\web\Response;

class DefaultControllerStartParticipationTest extends TestCase
{
    private $oldRequest;
    private $oldResponse;
    private $oldSession;

    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }

        $this->oldRequest = Yii::$app->get('request');
        $this->oldResponse = Yii::$app->get('response');
        $this->oldSession = Yii::$app->get('session');

        Yii::$app->set('request', new Request());
        Yii::$app->set('response', new Response());
        Yii::$app->set('session', new FakeSession());
        Yii::$app->response->format = Response::FORMAT_HTML;
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        Yii::$app->session->removeAllFlashes();
        Yii::$app->set('request', $this->oldRequest);
        Yii::$app->set('response', $this->oldResponse);
        Yii::$app->set('session', $this->oldSession);

        parent::tearDown();
    }

    public function testStartParticipationReturnsValidationMessageForInvalidCpf(): void
    {
        $_POST['cpf'] = '111.111.111-11';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $controller = new DefaultControllerForTest('default', Yii::$app);
        $controller->form = new TestStartParticipationForm();

        $result = $controller->actionStartParticipation();

        $this->assertFalse($result['success']);
        $this->assertSame('CPF invalido. Verifique o numero informado e tente novamente.', $result['message']);
    }

    public function testStartParticipationRedirectsExistingCpfToLogin(): void
    {
        $_POST['cpf'] = '529.982.247-25';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $controller = new DefaultControllerForTest('default', Yii::$app);
        $controller->form = new TestStartParticipationForm();
        $controller->form->userToReturn = $this->createMock(\app\models\User::class);

        $result = $controller->actionStartParticipation();

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('r=auth%2Flogin', $result['redirectUrl']);
        $this->assertNotFalse(Yii::$app->session->getFlash('info', false));
    }

    public function testStartParticipationRedirectsUnknownCpfToRegister(): void
    {
        $_POST['cpf'] = '529.982.247-25';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $controller = new DefaultControllerForTest('default', Yii::$app);
        $controller->form = new TestStartParticipationForm();

        $result = $controller->actionStartParticipation();

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('r=participante%2Fregister%2Findex', $result['redirectUrl']);
        $this->assertNotFalse(Yii::$app->session->getFlash('info', false));
    }

    public function testStartParticipationReturnsTechnicalErrorMessage(): void
    {
        $_POST['cpf'] = '529.982.247-25';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

        $controller = new DefaultControllerForTest('default', Yii::$app);
        $controller->form = new TestStartParticipationForm();
        $controller->form->throwOnLookup = true;

        $result = $controller->actionStartParticipation();

        $this->assertFalse($result['success']);
        $this->assertSame('Nao foi possivel validar o CPF neste momento. Tente novamente em instantes.', $result['message']);
    }

    public function testStartParticipationWithoutAjaxRedirectsBackToIndexOnValidationFailure(): void
    {
        $_POST['cpf'] = '111.111.111-11';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $controller = new DefaultControllerForTest('default', Yii::$app);
        $controller->form = new TestStartParticipationForm();

        $result = $controller->actionStartParticipation();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertStringContainsString('/participante/default/index', $result->headers->get('location', ''));
        $this->assertSame('CPF invalido. Verifique o numero informado e tente novamente.', Yii::$app->session->getFlash('error'));
    }
}

class DefaultControllerForTest extends DefaultController
{
    public ?TestStartParticipationForm $form = null;

    protected function createStartParticipationForm(): ParticipantStartParticipationForm
    {
        return $this->form ?? new TestStartParticipationForm();
    }
}

class TestStartParticipationForm extends ParticipantStartParticipationForm
{
    public ?\app\models\User $userToReturn = null;
    public bool $throwOnLookup = false;

    protected function lookupUserByCpf(string $cpf): ?\app\models\User
    {
        if ($this->throwOnLookup) {
            throw new \RuntimeException('falha simulada');
        }

        return $this->userToReturn;
    }
}

class FakeSession extends Component
{
    private array $flashes = [];

    public function setFlash($key, $value = true, $removeAfterAccess = true): void
    {
        $this->flashes[$key] = $value;
    }

    public function getFlash($key, $defaultValue = null, $delete = true)
    {
        if (!array_key_exists($key, $this->flashes)) {
            return $defaultValue;
        }

        $value = $this->flashes[$key];
        if ($delete) {
            unset($this->flashes[$key]);
        }

        return $value;
    }

    public function removeAllFlashes(): void
    {
        $this->flashes = [];
    }
}
