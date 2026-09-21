<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Html;

class User extends ActiveRecord implements IdentityInterface
{
    public $password;

    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;
    public const ROLE_PARTICIPANTE = 'participante';
    public const ROLE_ADMIN = 'admin';

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['nome', 'email', 'username', 'cpf', 'role'], 'required'],
            [['password'], 'required', 'on' => 'create'],
            [['status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nome', 'email', 'password_hash', 'authKey', 'password_reset_token'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 80],
            [['cpf'], 'string', 'max' => 14],
            [['email'], 'email'],
            [['password'], 'string', 'min' => 6],
            [['cpf'], 'unique'],
            [['email'], 'unique'],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['cpf'], 'validateCpf'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['role', 'in', 'range' => array_keys(self::getRoleList())],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'username' => 'Username',
            'cpf' => 'CPF',
            'password' => 'Senha',
            'password_hash' => 'Hash da senha',
            'password_reset_token' => 'Token de redefinição de senha',
            'role' => 'Perfil',
            'status' => 'Status',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword($password)
    {
        $hash = $this->password_hash;
        if (!is_string($hash) || $hash === '') {
            return false;
        }

        try {
            return Yii::$app->security->validatePassword($password, $hash);
        } catch (InvalidArgumentException $e) {
            $normalizedHash = $this->normalizeLegacyPasswordHash($hash);
            if ($normalizedHash === null) {
                Yii::warning('Hash de senha invalido para o usuario ID ' . ($this->id ?? 'novo') . '.', __METHOD__);
                return false;
            }

            $isValid = Yii::$app->security->validatePassword($password, $normalizedHash);
            if ($isValid && !$this->getIsNewRecord()) {
                $this->updateAttributes([
                    'password_hash' => $normalizedHash,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $this->password_hash = $normalizedHash;
            }

            return $isValid;
        }
    }

    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken(): void
    {
        $this->password_reset_token = null;
    }

    public static function isPasswordResetTokenValid(?string $token): bool
    {
        if (empty($token) || !is_string($token)) {
            return false;
        }

        $separatorPosition = strrchr($token, '_');
        if ($separatorPosition === false) {
            return false;
        }

        $timestamp = (int) substr($separatorPosition, 1);
        if ($timestamp <= 0) {
            return false;
        }

        $expire = (int) (Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600);

        return $timestamp + $expire >= time();
    }

    public static function findByPasswordResetToken(string $token): ?self
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    private function normalizeLegacyPasswordHash(string $hash): ?string
    {
        if (preg_match('/^\$2([axy])\$2([axy])\$(.+)$/', $hash, $matches) && $matches[1] === $matches[2]) {
            return '$2' . $matches[1] . '$' . $matches[3];
        }

        return null;
    }

    public function getParticipacoes()
    {
        return $this->hasMany(\app\modules\common\models\Participacao::class, ['user_id' => 'id']);
    }

    public function getParticipante()
    {
        return $this->hasOne(\app\modules\common\models\Participante::class, ['user_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" nao implementado.');
    }

    public static function findByCpf($cpf)
    {
        $cpf = preg_replace('/\D/', '', (string) $cpf);
        if ($cpf === '') {
            return null;
        }

        $cpfFormatado = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['or', ['cpf' => $cpf], ['cpf' => $cpfFormatado]])
            ->one();
    }

    public static function findByEmail($email)
    {
        return static::findOne([
            'email' => $email,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validateCpf($attribute)
    {
        $cpf = preg_replace('/\D/', '', (string) $this->$attribute);

        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            $this->addError($attribute, 'CPF invalido.');
            return;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($c = 0; $c < $t; $c++) {
                $sum += $cpf[$c] * (($t + 1) - $c);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $cpf[$c] !== $digit) {
                $this->addError($attribute, 'CPF invalido.');
                return;
            }
        }
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->cpf !== null) {
            $this->cpf = preg_replace('/\D/', '', (string) $this->cpf);
        }

        return true;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->isNewRecord) {
            $this->created_at = date('Y-m-d H:i:s');
            if (empty($this->authKey)) {
                $this->generateAuthKey();
            }
        }

        $this->updated_at = date('Y-m-d H:i:s');

        return true;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_INACTIVE => 'Inativo',
        ];
    }

    public static function getRoleList()
    {
        return [
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_PARTICIPANTE => 'Participante',
        ];
    }

    public function isParticipante()
    {
        return $this->role === self::ROLE_PARTICIPANTE;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getName()
    {
        return $this->nome ?: $this->email;
    }

    public function getCpfFormatado()
    {
        if (empty($this->cpf)) {
            return null;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => Html::tag('span', 'Ativo', ['class' => 'badge badge-success']),
            self::STATUS_INACTIVE => Html::tag('span', 'Inativo', ['class' => 'badge badge-danger']),
            default => Html::tag('span', 'Desconhecido', ['class' => 'badge badge-secondary']),
        };
    }
}
