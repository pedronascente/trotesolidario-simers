<?php

namespace tests\unit\models;

require_once dirname(__DIR__, 3) . '/models/LoginForm.php';
require_once dirname(__DIR__, 3) . '/models/User.php';

use app\models\LoginForm;
use app\models\User;

class LoginFormTest extends \Codeception\Test\Unit
{
    private $model;

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    public function testLoginNoUser(): void
    {
        $this->model = new LoginFormForTest(null, [
            'cpf' => '52998224725',
            'password' => 'not_existing_password',
        ]);

        $this->assertFalse($this->model->login());
        $this->assertTrue(\Yii::$app->user->isGuest);
    }

    public function testLoginWrongPassword(): void
    {
        $user = $this->createUser('correct_password');
        $this->model = new LoginFormForTest($user, [
            'cpf' => $user->cpf,
            'password' => 'wrong_password',
        ]);

        $this->assertFalse($this->model->login());
        $this->assertTrue(\Yii::$app->user->isGuest);
        $this->assertArrayHasKey('password', $this->model->errors);
    }

    public function testLoginCorrect(): void
    {
        $user = $this->createUser('correct_password');
        $this->model = new LoginFormForTest($user, [
            'cpf' => $user->cpf,
            'password' => 'correct_password',
        ]);

        $this->assertTrue($this->model->login());
        $this->assertFalse(\Yii::$app->user->isGuest);
        $this->assertArrayNotHasKey('password', $this->model->errors);
    }

    private function createUser(string $password): UserForLoginTest
    {
        $user = new UserForLoginTest();
        $user->id = 100;
        $user->cpf = '52998224725';
        $user->status = User::STATUS_ACTIVE;
        $user->authKey = 'test-auth-key';
        $user->setPassword($password);

        return $user;
    }
}

class LoginFormForTest extends LoginForm
{
    private ?User $testUser;

    public function __construct(?User $testUser, $config = [])
    {
        $this->testUser = $testUser;
        parent::__construct($config);
    }

    protected function getUser()
    {
        return $this->testUser;
    }
}

class UserForLoginTest extends User
{
    public static function primaryKey(): array
    {
        return ['id'];
    }

    public function attributes(): array
    {
        return ['id', 'cpf', 'status', 'authKey', 'password_hash'];
    }
}
