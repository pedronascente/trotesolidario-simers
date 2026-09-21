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
