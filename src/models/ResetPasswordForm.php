<?php

namespace app\models;

use yii\base\InvalidArgumentException;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    public $password_confirmation;

    private $_user;

    public function __construct($token, $config = [])
    {
        if (!User::isPasswordResetTokenValid($token)) {
            throw new InvalidArgumentException('O link de redefinição de senha é inválido ou expirou.');
        }

        $user = User::findByPasswordResetToken($token);
        if ($user === null) {
            throw new InvalidArgumentException('O link de redefinição de senha é inválido ou expirou.');
        }

        $this->_user = $user;
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['password', 'password_confirmation'], 'required'],
            [['password', 'password_confirmation'], 'string', 'min' => 6],
            ['password_confirmation', 'compare', 'compareAttribute' => 'password', 'message' => 'A confirmação da senha deve ser igual à senha.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Nova senha',
            'password_confirmation' => 'Confirmação da senha',
        ];
    }

    public function resetPassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->_user->setPassword($this->password);
        $this->_user->generateAuthKey();
        $this->_user->removePasswordResetToken();

        return $this->_user->save(false, ['password_hash', 'authKey', 'password_reset_token', 'updated_at']);
    }
}