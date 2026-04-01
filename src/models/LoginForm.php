<?php
 
namespace app\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $cpf;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            ['cpf', 'required', 'message' => 'O campo "CPF" não pode ficar em branco.'],
            ['password', 'required', 'message' => 'O campo "senha" não pode ficar em branco.'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'CPF ou senha inválidos.');
        }
    }

    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        return Yii::$app->user->login(
            $this->getUser(),
            $this->rememberMe ? 3600 * 24 * 30 : 0
        );
    }

    protected function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByCpf($this->cpf);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'cpf' => 'CPF',
            'password' => 'Senha',
            'rememberMe' => 'Manter Conectado',
        ];
    }
}