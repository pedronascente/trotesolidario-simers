<?php

namespace tests\unit\models;

require_once dirname(__DIR__, 3) . '/models/User.php';

use app\models\User;
use yii\base\NotSupportedException;

class UserTest extends \Codeception\Test\Unit
{
    public function testPasswordAndAuthKeyValidation(): void
    {
        $user = new UserForIdentityTest();
        $user->id = 100;
        $user->authKey = 'test100key';
        $user->setPassword('correct-password');

        $this->assertSame(100, $user->getId());
        $this->assertTrue($user->validateAuthKey('test100key'));
        $this->assertFalse($user->validateAuthKey('invalid-key'));
        $this->assertTrue($user->validatePassword('correct-password'));
        $this->assertFalse($user->validatePassword('wrong-password'));
    }

    public function testFindByAccessTokenIsExplicitlyUnsupported(): void
    {
        $this->expectException(NotSupportedException::class);
        User::findIdentityByAccessToken('100-token');
    }

    public function testPasswordResetTokenValidity(): void
    {
        $this->assertTrue(User::isPasswordResetTokenValid('token_' . time()));
        $this->assertFalse(User::isPasswordResetTokenValid('token_' . (time() - 7200)));
        $this->assertFalse(User::isPasswordResetTokenValid('invalid-token'));
    }

    public function testCreateScenarioAcceptsPasswordAlongWithGeneratedHash(): void
    {
        $user = new UserForCreateValidationTest();
        $user->scenario = 'create';
        $user->nome = 'Usuario Teste';
        $user->email = 'usuario@teste.com.br';
        $user->username = 'usuario.teste';
        $user->cpf = '52998224725';
        $user->password = 'senha123';
        $user->role = User::ROLE_PARTICIPANTE;
        $user->status = User::STATUS_ACTIVE;
        $user->setPassword($user->password);

        $user->validate();

        $this->assertSame([], $user->getErrors());
        $this->assertNotEmpty($user->password_hash);
    }
}

class UserForIdentityTest extends User
{
    public static function primaryKey(): array
    {
        return ['id'];
    }

    public function attributes(): array
    {
        return ['id', 'authKey', 'password_hash'];
    }
}

class UserForCreateValidationTest extends User
{
    public function rules(): array
    {
        return array_values(array_filter(parent::rules(), static function (array $rule): bool {
            return !in_array($rule[1] ?? null, ['unique', 'email'], true);
        }));
    }

    public function attributes(): array
    {
        return [
            'id', 'nome', 'email', 'username', 'cpf', 'password_hash',
            'role', 'authKey', 'password_reset_token', 'status', 'created_at', 'updated_at',
        ];
    }
}
