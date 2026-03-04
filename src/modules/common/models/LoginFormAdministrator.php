<?php

namespace app\modules\common\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginFormAdministrator extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['username', 'required', 'message' => 'O campo "email" não pode ficar em branco.'],
            //['username', 'required'],
            ['password', 'required', 'message' => 'O campo "senha" não pode ficar em branco.'],
            //['password', 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Email'),
            'rememberMe' => Yii::t('app', 'Manter Conectado'),
            'password' => Yii::t('app', 'Senha'),
        ];
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Usuário ou senha invalida.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {

            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            if (Users::findByEmail($this->username)) {
                return $this->_user = Users::findByEmailAdministrator($this->username);
            }
            $this->_user = Users::findByUsernameAdministrator($this->username);
        }

        return $this->_user;
    }
}
