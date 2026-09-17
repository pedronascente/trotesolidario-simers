<?php

namespace tests\unit\modules\participante\controllers;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/participante/controllers/RegisterController.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/ParticipantRegistrationForm.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\ParticipantRegistrationForm;
use app\modules\participante\controllers\RegisterController;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\web\Application;
use yii\web\Session;

class RegisterControllerTest extends TestCase
{
    private $oldSession;

    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }

        $this->oldSession = Yii::$app->session;
        Yii::$app->set('session', new RegisterControllerFakeSession());
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        Yii::$app->set('session', $this->oldSession);

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

    public function testActionIndexLogsInAndRedirectsAfterSuccessfulRegistration(): void
    {
        $_POST = ['ParticipantRegistrationForm' => ['name' => 'Aluno Teste']];

        $controller = new TestRegisterController('register', Yii::$app);
        $controller->registrationForm = new StubParticipantRegistrationForm();
        $controller->registrationForm->loadResult = true;
        $controller->registrationForm->userToReturn = new User();
        $controller->loginResult = true;

        $result = $controller->actionIndex();

        $this->assertSame('redirected', $result);
        $this->assertSame(['/participante/default/home'], $controller->redirectRoute);
        $this->assertInstanceOf(User::class, $controller->loggedUser);
        $this->assertSame('Cadastro realizado com sucesso.', Yii::$app->session->getFlash('success'));
    }

    public function testActionIndexKeepsRenderingWhenRegistrationFails(): void
    {
        $_POST = ['ParticipantRegistrationForm' => ['name' => 'Aluno Teste']];

        $controller = new TestRegisterController('register', Yii::$app);
        $controller->registrationForm = new StubParticipantRegistrationForm();
        $controller->registrationForm->loadResult = true;
        $controller->registrationForm->userToReturn = null;

        $result = $controller->actionIndex();

        $this->assertSame('rendered', $result);
        $this->assertNull($controller->loggedUser);
    }
}

class TestRegisterController extends RegisterController
{
    public string $capturedView = '';
    public array $capturedParams = [];
    public ?ParticipantRegistrationForm $registrationForm = null;
    public $loggedUser = null;
    public bool $loginResult = false;
    public array $redirectRoute = [];

    public function render($view, $params = [])
    {
        $this->capturedView = $view;
        $this->capturedParams = $params;

        return 'rendered';
    }

    public function redirect($url, $statusCode = 302)
    {
        $this->redirectRoute = $url;

        return 'redirected';
    }

    protected function createRegistrationForm(): ParticipantRegistrationForm
    {
        return $this->registrationForm ?? new StubParticipantRegistrationForm();
    }

    protected function loginUser($user): bool
    {
        $this->loggedUser = $user;

        return $this->loginResult;
    }
}

class StubParticipantRegistrationForm extends ParticipantRegistrationForm
{
    public bool $loadResult = false;
    public ?User $userToReturn = null;

    public function load($data, $formName = null): bool
    {
        return $this->loadResult;
    }

    public function register(): ?User
    {
        return $this->userToReturn;
    }
}

class RegisterControllerFakeSession extends Session
{
    private array $flashes = [];

    public function getHasSessionId(): bool
    {
        return true;
    }

    public function open()
    {
        return true;
    }

    public function setFlash($key, $value = true, $removeAfterAccess = true): void
    {
        $this->flashes[$key] = $value;
    }

    public function getFlash($key, $defaultValue = null, $delete = false)
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
}
