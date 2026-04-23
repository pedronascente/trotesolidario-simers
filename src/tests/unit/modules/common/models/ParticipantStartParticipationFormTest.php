<?php

namespace tests\unit\modules\common\models;

require_once dirname(__DIR__, 5) . '/modules/common/models/ParticipantStartParticipationForm.php';
require_once dirname(__DIR__, 5) . '/models/User.php';

use app\models\User;
use app\modules\common\models\ParticipantStartParticipationForm;
use PHPUnit\Framework\TestCase;

class ParticipantStartParticipationFormTest extends TestCase
{
    public function testCpfIsRequired(): void
    {
        $form = new TestParticipantStartParticipationForm();

        $this->assertFalse($form->validate());
        $this->assertSame('Informe o CPF para continuar.', $form->getFirstError('cpf'));
    }

    public function testCpfMustBeValid(): void
    {
        $form = new TestParticipantStartParticipationForm();
        $form->cpf = '111.111.111-11';

        $this->assertFalse($form->validate());
        $this->assertSame('CPF invalido. Verifique o numero informado e tente novamente.', $form->getFirstError('cpf'));
    }

    public function testFindExistingUserAcceptsMaskedCpf(): void
    {
        $existingUser = $this->createMock(User::class);

        $form = new TestParticipantStartParticipationForm();
        $form->cpf = '529.982.247-25';
        $form->userToReturn = $existingUser;

        $this->assertTrue($form->validate());
        $this->assertSame($existingUser, $form->findExistingUser());
        $this->assertSame('52998224725', $form->capturedLookupCpf);
        $this->assertSame('529.982.247-25', $form->getFormattedCpf());
    }
}

class TestParticipantStartParticipationForm extends ParticipantStartParticipationForm
{
    public ?User $userToReturn = null;
    public string $capturedLookupCpf = '';

    protected function lookupUserByCpf(string $cpf): ?User
    {
        $this->capturedLookupCpf = $cpf;

        return $this->userToReturn;
    }
}
