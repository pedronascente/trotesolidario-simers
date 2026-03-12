<?php

namespace app\modules\common\models;

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

    public function attributeLabels()
    {
        return [
            'cpf' => 'CPF',
            'password' => 'Senha',
            'rememberMe' => 'Manter Conectado',
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !Yii::$app->getSecurity()->validatePassword($this->password, $user->passwordHash)) {
                $this->addError($attribute, 'CPF ou senha inválidos.');
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? 3600 * 24 * 30 : 0
            );
        }
        return false;
    }

 

    protected function getUser()
    {
        if ($this->_user === false) {
            $cpfLimpo = preg_replace('/[^0-9]/', '', $this->cpf);
            $this->_user = Users::findOne(['cpf' => $cpfLimpo, 'status' => 1]);
        }
        return $this->_user;
    }
}
