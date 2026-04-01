<?php

namespace app\modules\common\models;

use app\models\User;
use yii\base\Model;

class UserCreateForm extends Model
{
    public $id;
    public $nome;
    public $email;
    public $username;
    public $cpf;
    public $password;
    public $role;
    public $status;
    public $create_participante;
    public $estudante;
    public $estudante_medicina;
    public $previsao_formatura;

    public function rules()
    {
        return [
            [['nome', 'email', 'username', 'cpf', 'role'], 'required'],
            [['password'], 'required', 'on' => 'create'],
            [['id', 'status', 'estudante', 'estudante_medicina', 'create_participante'], 'integer'],
            [['previsao_formatura'], 'safe'],
            [['nome', 'email'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 80],
            [['cpf'], 'string', 'max' => 14],
            [['password'], 'string', 'min' => 6],
            [['email'], 'email'],
            [['role'], 'in', 'range' => array_keys(User::getRoleList())],
            [['status'], 'in', 'range' => array_keys(User::getStatusList())],
            [['cpf'], 'validateCpf'],
            [['email'], 'validateUniqueEmail'],
            [['cpf'], 'validateUniqueCpf'],
            [['username'], 'validateUniqueUsername'],
            [['create_participante'], 'validateParticipantRoleConsistency'],
            [['previsao_formatura'], 'required', 'when' => function (self $model) {
                return (int) $model->create_participante === 1 && (int) $model->estudante === 1;
            }, 'whenClient' => "function () { return $('#usercreateform-create_participante').is(':checked') && $('#usercreateform-estudante').val() === '1'; }"],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $fields = [
            'id',
            'nome',
            'email',
            'username',
            'cpf',
            'password',
            'role',
            'status',
            'create_participante',
            'estudante',
            'estudante_medicina',
            'previsao_formatura',
        ];

        $scenarios['create'] = $fields;
        $scenarios['update'] = $fields;

        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'nome' => 'Nome',
            'email' => 'E-mail',
            'username' => 'Username',
            'cpf' => 'CPF',
            'password' => 'Senha',
            'role' => 'Perfil',
            'status' => 'Status',
            'create_participante' => 'Criar perfil de participante',
            'estudante' => 'Estudante',
            'estudante_medicina' => 'Estudante de medicina',
            'previsao_formatura' => 'Previsao de formatura',
        ];
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->cpf !== null) {
            $this->cpf = preg_replace('/\D/', '', (string) $this->cpf);
        }

        $this->create_participante = (int) $this->create_participante;
        $this->estudante = (int) $this->estudante;
        $this->estudante_medicina = (int) $this->estudante_medicina;
        $this->status = $this->status === null || $this->status === '' ? User::STATUS_ACTIVE : (int) $this->status;

        if ($this->role === User::ROLE_ADMIN) {
            $this->create_participante = 0;
        }

        return true;
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

    public function validateUniqueEmail($attribute)
    {
        $this->validateUniqueAttribute($attribute, $this->$attribute, 'Este e-mail ja esta em uso.');
    }

    public function validateUniqueCpf($attribute)
    {
        $this->validateUniqueAttribute($attribute, $this->$attribute, 'Este CPF ja esta em uso.');
    }

    public function validateUniqueUsername($attribute)
    {
        $this->validateUniqueAttribute($attribute, $this->$attribute, 'Este username ja esta em uso.');
    }

    private function validateUniqueAttribute($attribute, $value, $message): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        $query = User::find()->where([$attribute => $value]);
        if ($this->id) {
            $query->andWhere(['<>', 'id', $this->id]);
        }

        if ($query->exists()) {
            $this->addError($attribute, $message);
        }
    }

    public function validateParticipantRoleConsistency($attribute): void
    {
        if ($this->role === User::ROLE_ADMIN && (int) $this->$attribute === 1) {
            $this->addError($attribute, 'Perfis administrativos nao devem possuir cadastro de participante.');
        }
    }
}
