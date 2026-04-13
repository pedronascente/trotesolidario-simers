<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 5) . '/modules/common/models/ParticipantRegistrationForm.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participante.php';
require_once dirname(__DIR__, 5) . '/modules/common/models/Participacao.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\Participacao;
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

    public function testStudentRegistrationRequiresInstitutionAndTrote(): void
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

        $this->assertFalse($form->validate());
        $this->assertNotEmpty($form->getFirstError('trote_id'));
        $this->assertNotEmpty($form->getFirstError('instituicao'));
    }

    public function testRegisterCreatesUserParticipanteAndParticipacao(): void
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
        $form->instituicao = 5;
        $form->trote_id = 7;
        $form->previsaoFormatura = '2028/01';
        $form->politicaPrivacidade = 1;
        $form->politicaImagem = 1;

        $user = $form->register();

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(User::ROLE_PARTICIPANTE, $user->role);
        $this->assertSame('aluno@example.com', $user->email);
        $this->assertNotEmpty($user->username);
        $this->assertTrue($form->savedUser);
        $this->assertTrue($form->savedParticipante);
        $this->assertTrue($form->savedParticipacao);
        $this->assertSame('Enfermagem', $form->capturedParticipacao->curso);
        $this->assertSame(5, $form->capturedParticipacao->universidade_id);
        $this->assertSame(7, $form->capturedParticipacao->trote_id);
    }
}

class TestParticipantRegistrationForm extends ParticipantRegistrationForm
{
    public bool $savedUser = false;
    public bool $savedParticipante = false;
    public bool $savedParticipacao = false;
    public ?FakeParticipacao $capturedParticipacao = null;

    protected function createUserModel(): User
    {
        return new FakeUser();
    }

    protected function createParticipanteModel(): Participante
    {
        return new FakeParticipante();
    }

    protected function createParticipacaoModel(): Participacao
    {
        return new FakeParticipacao();
    }

    protected function cpfValidationErrors(string $cpf): array
    {
        return [];
    }

    protected function emailExists(string $email): bool
    {
        return false;
    }

    protected function cpfExists(string $cpf): bool
    {
        return false;
    }

    protected function usernameExists(string $username): bool
    {
        return false;
    }

    protected function troteIsAtivo(int $troteId): bool
    {
        return true;
    }

    protected function universidadeIsAtiva(int $universidadeId): bool
    {
        return true;
    }

    protected function saveUserModel(User $user): bool
    {
        $this->savedUser = true;
        $user->id = 99;
        return true;
    }

    protected function saveParticipanteModel(Participante $participante): bool
    {
        $this->savedParticipante = true;
        return true;
    }

    protected function saveParticipacaoModel(Participacao $participacao): bool
    {
        $this->savedParticipacao = true;
        $this->capturedParticipacao = $participacao;
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

class FakeParticipacao extends Participacao
{
    public function attributes(): array
    {
        return ['id', 'user_id', 'trote_id', 'universidade_id', 'curso', 'status', 'created_at', 'updated_at'];
    }
}
