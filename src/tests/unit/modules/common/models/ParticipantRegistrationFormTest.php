<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 4) . '/_bootstrap.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/ParticipantRegistrationForm.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participante.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\Participante;
use app\modules\common\models\ParticipantRegistrationForm;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\db\Connection;
use yii\db\Transaction;
use yii\web\Application;

class ParticipantRegistrationFormTest extends TestCase
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

    public function testStudentRegistrationRequiresMedicineChoiceAndGraduationForecast(): void
    {
        $form = new TestParticipantRegistrationForm();
        $form->name = 'Aluno Teste';
        $form->cpf = '529.982.247-25';
        $form->password = 'segredo123';
        $form->email = 'aluno@example.com';
        $form->estudante = 'Sim';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $this->assertFalse($form->validate());
        $this->assertNotEmpty($form->getFirstError('estudanteMedicina'));
        $this->assertNotEmpty($form->getFirstError('previsaoFormatura'));
    }

    public function testStudentRegistrationRequiresCourseWhenNotMedicine(): void
    {
        $form = new TestParticipantRegistrationForm();
        $form->name = 'Aluno Teste';
        $form->cpf = '529.982.247-25';
        $form->password = 'segredo123';
        $form->email = 'aluno@example.com';
        $form->estudante = 'Sim';
        $form->estudanteMedicina = 'Nao';
        $form->previsaoFormatura = '2028/01';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $this->assertFalse($form->validate());
        $this->assertSame('Informe o curso.', $form->getFirstError('estudanteOutros'));
    }

    public function testStudentRegistrationDoesNotRequireCourseWhenMedicineStudent(): void
    {
        $form = new TestParticipantRegistrationForm();
        $form->name = 'Aluno Teste';
        $form->cpf = '529.982.247-25';
        $form->password = 'segredo123';
        $form->email = 'aluno@example.com';
        $form->estudante = 'Sim';
        $form->estudanteMedicina = 'Sim';
        $form->previsaoFormatura = '2028/01';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $this->assertTrue($form->validate());
        $this->assertNull($form->getFirstError('estudanteOutros'));
    }

    public function testRegisterCreatesUserAndParticipanteWithoutParticipacao(): void
    {
        $transaction = $this->createMock(Transaction::class);
        $transaction->expects($this->once())->method('commit');
        $transaction->expects($this->never())->method('rollBack');

        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['beginTransaction'])
            ->getMock();
        $db->method('beginTransaction')->willReturn($transaction);

        $this->oldDb = Yii::$app->db;
        Yii::$app->set('db', $db);

        $form = new TestParticipantRegistrationForm();
        $form->name = 'Aluno Teste';
        $form->cpf = '529.982.247-25';
        $form->password = 'segredo123';
        $form->email = 'aluno@example.com';
        $form->estudante = 'Sim';
        $form->estudanteMedicina = 'Nao';
        $form->estudanteOutros = 'Enfermagem';
        $form->previsaoFormatura = '2028/01';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $user = $form->register();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(User::ROLE_PARTICIPANTE, $user->role);
        $this->assertSame('aluno@example.com', $user->email);
        $this->assertSame('aluno.teste', $user->username);
        $this->assertSame('segredo123', $form->capturedUser->password);
        $this->assertNotEmpty($form->capturedUser->password_hash);
        $this->assertTrue($form->savedUser);
        $this->assertTrue($form->savedParticipante);
        $this->assertFalse($form->savedParticipacao);
    }

    public function testRegisterFailsWhenEmailAlreadyExists(): void
    {
        $form = new TestParticipantRegistrationForm();
        $form->existingEmails = ['duplicado@example.com'];
        $form->name = 'Aluno Teste';
        $form->cpf = '529.982.247-25';
        $form->password = 'segredo123';
        $form->email = 'duplicado@example.com';
        $form->estudante = 'Nao';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $this->assertNull($form->register());
        $this->assertSame('Este e-mail ja esta em uso.', $form->getFirstError('email'));
        $this->assertFalse($form->savedUser);
    }

    public function testRegisterFailsWhenCpfAlreadyExists(): void
    {
        $form = new TestParticipantRegistrationForm();
        $form->existingCpfs = ['52998224725'];
        $form->name = 'Aluno Teste';
        $form->cpf = '529.982.247-25';
        $form->password = 'segredo123';
        $form->email = 'aluno@example.com';
        $form->estudante = 'Nao';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $this->assertNull($form->register());
        $this->assertSame('Este CPF ja esta em uso.', $form->getFirstError('cpf'));
        $this->assertFalse($form->savedUser);
    }

    public function testRegisterFailsWhenValidationIsInvalid(): void
    {
        $form = new TestParticipantRegistrationForm();
        $form->name = 'Aluno Teste';
        $form->cpf = '529.982.247-25';
        $form->password = '123';
        $form->email = 'email-invalido';
        $form->estudante = 'Nao';

        $this->assertNull($form->register());
        $this->assertNotEmpty($form->getFirstError('email'));
        $this->assertNotEmpty($form->getFirstError('password'));
        $this->assertNotEmpty($form->getFirstError('politicaPrivacidade'));
        $this->assertNotEmpty($form->getFirstError('politicaImagem'));
    }

    public function testRegisterGeneratesUniqueUsernameAfterTruncation(): void
    {
        $transaction = $this->createMock(Transaction::class);
        $transaction->expects($this->once())->method('commit');
        $transaction->expects($this->never())->method('rollBack');

        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['beginTransaction'])
            ->getMock();
        $db->method('beginTransaction')->willReturn($transaction);

        $this->oldDb = Yii::$app->db;
        Yii::$app->set('db', $db);

        $base = str_repeat('a', 90);

        $form = new TestParticipantRegistrationForm();
        $form->existingUsernames = [
            substr($base, 0, 80),
        ];
        $form->name = $base . ' ' . $base;
        $form->cpf = '529.982.247-25';
        $form->password = 'segredo123';
        $form->email = 'aluno@example.com';
        $form->estudante = 'Nao';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $user = $form->register();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(substr($base, 0, 78) . '.1', $user->username);
    }
}

class TestParticipantRegistrationForm extends ParticipantRegistrationForm
{
    public bool $savedUser = false;
    public bool $savedParticipante = false;
    public bool $savedParticipacao = false;
    public ?User $capturedUser = null;
    public array $existingEmails = [];
    public array $existingCpfs = [];
    public array $existingUsernames = [];

    protected function createUserModel(): User
    {
        return new FakeUser();
    }

    protected function createParticipanteModel(): Participante
    {
        return new FakeParticipante();
    }

    protected function cpfValidationErrors(string $cpf): array
    {
        return [];
    }

    protected function emailExists(string $email): bool
    {
        return in_array($email, $this->existingEmails, true);
    }

    protected function cpfExists(string $cpf): bool
    {
        return in_array($cpf, $this->existingCpfs, true);
    }

    protected function usernameExists(string $username): bool
    {
        return in_array($username, $this->existingUsernames, true);
    }

    protected function saveUserModel(User $user): bool
    {
        $this->savedUser = true;
        $this->capturedUser = $user;
        $user->id = 99;
        return true;
    }

    protected function saveParticipanteModel(Participante $participante): bool
    {
        $this->savedParticipante = true;
        return true;
    }
}

class FakeUser extends User
{
    public function attributes(): array
    {
        return ['id', 'nome', 'email', 'username', 'cpf', 'password_hash', 'role', 'authKey', 'status', 'created_at', 'updated_at'];
    }
}

class FakeParticipante extends Participante
{
    public function attributes(): array
    {
        return ['id', 'user_id', 'estudante', 'estudante_medicina', 'previsao_formatura'];
    }
}
