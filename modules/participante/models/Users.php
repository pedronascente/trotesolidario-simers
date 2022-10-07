<?php

namespace app\modules\participante\models;

use Yii;
use \yii\web\IdentityInterface;
use \yii\base\NotSupportedException;
use app\modules\participante\models\Trote;

/**
 * This is the model class for table "_users".
 *
 * @property int $id
 * @property int|null $trote_id
 * @property string $name
 * @property string $passwordHash
 * @property string $email
 * @property int $status
 * @property int $administrator
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $username
 * @property string|null $passwordResetToken
 * @property string|null $authKey
 * @property string|null $estudante
 * @property string|null $instituicao
 * @property string|null $outraInstituicao
 * @property string|null $telefone
 * @property string|null $previsaoFormatura
 * @property string|null $conheceONas
 * @property string|null $politicaPrivacidade
 * @property string|null $politicaImagem
 *
 * @property Trote $trote
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['status', 'trote_id', 'administrator', 'instituicao'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['estudante', 'outraInstituicao', 'telefone', 'previsaoFormatura', 'conheceONas', 'politicaPrivacidade', 'politicaImagem'], 'string'],
            [['name'], 'string', 'max' => 250],
            [['passwordHash', 'username', 'passwordResetToken', 'authKey'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 100],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::className(), 'targetAttribute' => ['trote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trote_id' => 'Trote ID',
            'name' => 'Name',
            'passwordHash' => 'Password Hash',
            'email' => 'Email',
            'status' => 'Status',
            'administrator' => 'Administrator',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'username' => 'Username',
            'passwordResetToken' => 'Password Reset Token',
            'authKey' => 'Auth Key',
            'estudante' => 'Estudante',
            'instituicao' => 'Instituição',
            'outraInstituicao' => 'Outra Instituição',
            'telefone' => 'Telefone',
            'previsaoFormatura' => 'Previsão Formatura',
            'conheceONas' => 'Conhece O Nas',
            'politicaPrivacidade' => 'Estou de acordo com a política de privacidade',
            'politicaImagem' => 'Eu autorizo o usou de imagem, video e/ou voz'
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['username', 'email'];

        return $scenarios;
    }

    /**
     * Gets query for [[Trote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrote()
    {
        return $this->hasOne(Trote::className(), ['id' => 'trote_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    public static function findByUsernameAdministrator($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE, 'administrator' => 1]);
    }

    public static function findByEmail($username)
    {
        return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    public static function findByEmailAdministrator($username)
    {
        return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE, 'administrator' => 1]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {

        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'passwordResetToken' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {


        if ($token == "") {
            return false;
        }


        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwordResetToken = null;
    }

    public function requestPasswordResetToken($id)
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'id' => $id,
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->passwordResetToken)) {
            $user->generatePasswordResetToken();
        }

        if (!$user->save()) {
            return false;
        }

        return $user->passwordResetToken;
    }
    /**
     * Generates created_at from time now and sets it to the model
     *
     */
    public function setCreated()
    {
        $this->created_at =  date("Y-m-d H:i:s");
    }

    /**
     * Generates updated_at from time now and sets it to the model
     *
     */
    public function setUpdated()
    {
        $this->updated_at = date("Y-m-d H:i:s");
    }
}
