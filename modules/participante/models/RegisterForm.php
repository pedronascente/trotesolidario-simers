<?php

namespace app\modules\participante\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RegisterForm extends Model
{

    public $name;
    public $cpf;
    public $password;
    public $email;
    public $estudante;
    public $estudanteMedicina;
    public $estudanteOutros;
    public $instituicao;
    public $outraInstituicao;
    public $telefone;
    public $previsaoFormatura;
    public $conheceONas;
    public $politicaPrivacidade;
    public $politicaImagem;
    public $rememberMe = true;
    public $trote_id;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['name', 'required', 'message' => 'O campo "nome" não pode ficar em branco.'],
            ['name', 'required'],
            ['cpf', 'required', 'message' => 'O campo "CPF" não pode ficar em branco.'],
            ['cpf', 'required'],
            ['password', 'required', 'message' => 'O campo "senha" não pode ficar em branco.'],
            ['password', 'required'],
            ['email', 'required', 'message' => 'O campo "email" não pode ficar em branco'],
            ['email', 'required'],
            ['estudante', 'required', 'message' => 'Você deve definir se é estudante ou não'],
            ['estudante', 'required'],
            ['trote_id', 'required', 'message' => 'Você deve definir um trote'],
            ['trote_id', 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'name' => Yii::t('app', 'Nome Completo Para o Certificado'),
            'password' => Yii::t('app', 'Senha'),
            'email' => Yii::t('app', 'Email'),
            'estudante' => Yii::t('app', 'Estudante'),
            'estudanteMedicina' => 'Medicina ou OUTROS',
            'estudanteOutros' => 'Qual curso',
            'instituicao' => Yii::t('app', 'Instituição de Ensino'),
            'outraInstituicao' => Yii::t('app', 'Outra Instituição'),
            'telefone' => Yii::t('app', 'Telefone'),
            'trote_id' => Yii::t('app', 'Trote'),
            'cpf' => 'CPF',
            'previsaoFormatura' => Yii::t('app', 'ATM previsão de formatura ex: (2022/01)'),
            'politicaPrivacidade' => Yii::t('app', 'Estou de acordo com a política de privacidade.'),
            'politicaImagem' => Yii::t('app', 'Eu autorizo o usou de imagem, video e/ou voz.'),
        ];
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
            if (Users::findByEmail($this->email)) {
                return $this->_user = Users::findByEmail($this->email);
            }
            $this->_user = Users::findByUsername($this->email);
        }

        return $this->_user;
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
}
