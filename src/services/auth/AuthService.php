<?php

namespace app\services\auth;

use Yii;

use app\models\User;
use app\services\auth\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    public function login(string $cpf, string $password): bool
    {
        $user = User::findByCpf($cpf);

        if (!$user) {
            return false;
        }

        if (!$user->validatePassword($password)) {
            return false;
        }

        return Yii::$app->user->login($user);
    }
}