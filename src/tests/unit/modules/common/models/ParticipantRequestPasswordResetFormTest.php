<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 5) . '/modules/common/models/ParticipantRequestPasswordResetForm.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\ParticipantRequestPasswordResetForm;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\web\Application;

class ParticipantRequestPasswordResetFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (Yii::$app === null) {
            new Application(require dirname(__DIR__, 5) . '/config/test.php');
        }
    }

    public function testValidateFailsWhenEmailAndCpfDoNotMatch(): void
    {
        $form = new TestParticipantRequestPasswordResetForm(null);
        $form->email = 'participante@example.com';
        $form->cpf = '529.982.247-25';

        $this->assertFalse($form->validate());
        $this->assertSame('Nao encontramos um usuario ativo com o e-mail e CPF informados.', $form->getFirstError('email'));
    }

    public function testSendEmailPersistsTokenAndSendsMail(): void
    {
        $user = new PasswordResetUserForTest();
        $user->id = 12;
        $user->nome = 'Participante Teste';
        $user->email = 'participante@example.com';
        $user->cpf = '52998224725';
        $user->status = User::STATUS_ACTIVE;
        $user->password_reset_token = null;

        $form = new TestParticipantRequestPasswordResetForm($user);
        $form->email = 'participante@example.com';
        $form->cpf = '529.982.247-25';

        $this->assertTrue($form->sendEmail());
        $this->assertTrue($form->tokenSaved);
        $this->assertTrue($form->mailSent);
        $this->assertNotEmpty($user->password_reset_token);
    }
}

class TestParticipantRequestPasswordResetForm extends ParticipantRequestPasswordResetForm
{
    private ?User $user;
    public bool $tokenSaved = false;
    public bool $mailSent = false;

    public function __construct(?User $user, $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    public function validateCpf($attribute): void
    {
        $cpf = preg_replace('/\D/', '', (string) $this->$attribute);
        if ($cpf !== '52998224725') {
            $this->addError($attribute, 'CPF invalido.');
        }
    }

    protected function findUserByEmailAndCpf(string $email, string $cpf): ?User
    {
        if ($this->user === null) {
            return null;
        }

        return $this->user->email === $email && $this->user->cpf === $cpf ? $this->user : null;
    }

    protected function saveResetToken(User $user): bool
    {
        $this->tokenSaved = true;
        return true;
    }

    protected function sendResetEmail(User $user, string $resetLink): bool
    {
        $this->mailSent = str_contains($resetLink, 'token=');
        return true;
    }
}

class PasswordResetUserForTest extends User
{
    public static function primaryKey(): array
    {
        return ['id'];
    }

    public function attributes(): array
    {
        return ['id', 'nome', 'email', 'cpf', 'status', 'password_reset_token', 'updated_at'];
    }
}
