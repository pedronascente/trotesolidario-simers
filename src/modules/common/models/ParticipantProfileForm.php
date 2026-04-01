<?php

namespace app\modules\common\models;

use app\models\User;
use yii\base\Model;

class ParticipantProfileForm extends Model
{
    public ?int $userId = null;
    public $nome;
    public $email;
    public $username;
    public $cpf;
    public $password;
    public $password_confirmation;
    public $estudante;
    public $estudante_medicina;
    public $previsao_formatura;

    public function rules()
    {
        return [
            [['nome', 'email', 'username', 'cpf'], 'required'],
            [['email'], 'email'],
            [['nome', 'email'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 80],
            [['cpf'], 'string', 'max' => 14],
            [['password', 'password_confirmation'], 'string', 'min' => 6, 'skipOnEmpty' => true],
            [['password', 'password_confirmation'], 'validatePasswordPair'],
            [
                ['password_confirmation'],
                'compare',
                'compareAttribute' => 'password',
                'skipOnEmpty' => false,
                'when' => function (self $model) {
                    return $model->password !== '' || $model->password_confirmation !== '';
                },
                'message' => "A confirma\u{00E7}\u{00E3}o de senha n\u{00E3}o confere.",
            ],
            [['estudante', 'estudante_medicina'], 'integer'],
            [['previsao_formatura'], 'safe'],
            [['cpf'], 'validateCpf'],
            [['email'], 'validateUniqueEmail'],
            [['username'], 'validateUniqueUsername'],
            [['cpf'], 'validateUniqueCpf'],
            [['previsao_formatura'], 'validatePrevisaoFormatura'],
            [['estudante_medicina'], 'validateEstudanteMedicina'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nome' => 'Nome',
            'email' => 'E-mail',
            'username' => "Usu\u{00E1}rio",
            'cpf' => 'CPF',
            'password' => 'Nova senha',
            'password_confirmation' => "Confirma\u{00E7}\u{00E3}o da senha",
            'estudante' => 'Estudante',
            'estudante_medicina' => 'Estudante de medicina',
            'previsao_formatura' => "Previs\u{00E3}o de formatura",
        ];
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->nome = trim((string) $this->nome);
        $this->email = trim((string) $this->email);
        $this->username = trim((string) $this->username);
        $this->password = trim((string) $this->password);
        $this->password_confirmation = trim((string) $this->password_confirmation);

        if ($this->cpf !== null) {
            $this->cpf = preg_replace('/\D/', '', (string) $this->cpf);
        }

        $this->estudante = (int) $this->estudante;
        $this->estudante_medicina = (int) $this->estudante_medicina;

        if ($this->estudante !== 1) {
            $this->estudante_medicina = 0;
            $this->previsao_formatura = null;
        } elseif ($this->previsao_formatura === '') {
            $this->previsao_formatura = null;
        }

        return true;
    }

    public function validatePasswordPair($attribute): void
    {
        $password = (string) $this->password;
        $confirmation = (string) $this->password_confirmation;

        if ($password === '' && $confirmation === '') {
            return;
        }

        if ($password === '' && !$this->hasErrors('password')) {
            $this->addError('password', 'Informe a nova senha.');
        }

        if ($confirmation === '' && !$this->hasErrors('password_confirmation')) {
            $this->addError('password_confirmation', 'Confirme a nova senha.');
        }
    }

    public function validateCpf($attribute)
    {
        $user = new User();
        $user->$attribute = $this->$attribute;
        $user->validateCpf($attribute);

        foreach ($user->getErrors($attribute) as $error) {
            $this->addError($attribute, $error);
        }
    }

    public function validateUniqueEmail($attribute): void
    {
        if ($this->hasErrors($attribute) || $this->email === null || $this->email === '') {
            return;
        }

        $query = User::find()->where(['email' => $this->email]);
        if ($this->userId !== null) {
            $query->andWhere(['<>', 'id', $this->userId]);
        }

        if ($query->exists()) {
            $this->addError($attribute, "Este e-mail j\u{00E1} est\u{00E1} em uso.");
        }
    }

    public function validateUniqueUsername($attribute): void
    {
        if ($this->hasErrors($attribute) || $this->username === null || $this->username === '') {
            return;
        }

        $query = User::find()->where(['username' => $this->username]);
        if ($this->userId !== null) {
            $query->andWhere(['<>', 'id', $this->userId]);
        }

        if ($query->exists()) {
            $this->addError($attribute, "Este nome de usu\u{00E1}rio j\u{00E1} est\u{00E1} em uso.");
        }
    }

    public function validateUniqueCpf($attribute): void
    {
        if ($this->hasErrors($attribute) || $this->cpf === null || $this->cpf === '') {
            return;
        }

        $query = User::find()->where(['cpf' => $this->cpf]);
        if ($this->userId !== null) {
            $query->andWhere(['<>', 'id', $this->userId]);
        }

        if ($query->exists()) {
            $this->addError($attribute, "Este CPF j\u{00E1} est\u{00E1} em uso.");
        }
    }

    public function validatePrevisaoFormatura($attribute): void
    {
        if ((int) $this->estudante !== 1) {
            return;
        }

        if (empty($this->$attribute)) {
            $this->addError($attribute, "Informe a previs\u{00E3}o de formatura.");
            return;
        }

        if (strtotime((string) $this->$attribute) === false) {
            $this->addError($attribute, "Informe uma previs\u{00E3}o de formatura v\u{00E1}lida.");
        }
    }

    public function validateEstudanteMedicina($attribute): void
    {
        if ((int) $this->$attribute === 1 && (int) $this->estudante !== 1) {
            $this->addError($attribute, 'Para marcar estudante de medicina, o perfil precisa estar como estudante.');
        }
    }
}
