<?php

namespace app\modules\common\models;

use Yii;
use \yii\web\IdentityInterface;
use \yii\base\NotSupportedException;
use app\modules\common\models\Trote;


class Users extends \yii\db\ActiveRecord implements IdentityInterface{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 1;


    public static function tableName(){
        return '_users';
    }

    public function rules(){
        return [
            [['name', 'cpf'], 'required'],
            [['status', 'trote_id', 'administrator', 'instituicao'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['estudante', 'cpf', 'estudanteMedicina', 'estudanteOutros', 'outraInstituicao', 'telefone', 'previsaoFormatura', 'conheceONas', 'politicaPrivacidade', 'politicaImagem'], 'string'],
            [['name'], 'string', 'max' => 250],
            [['passwordHash', 'username', 'passwordResetToken', 'authKey'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 100],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::className(), 'targetAttribute' => ['trote_id' => 'id']],
        ];
    }

    public function attributeLabels(){
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
            'estudanteMedicina' => 'Medicina ou OUTROS',
            'estudanteOutros' => 'Qual curso',
            'instituicao' => 'Instituição',
            'outraInstituicao' => 'Outra Instituição',
            'telefone' => 'Telefone',
            'previsaoFormatura' => 'Previsão Formatura',
            'conheceONas' => 'Conhece O Nas',
            'cpf' => 'CPF',
            'politicaPrivacidade' => 'Estou de acordo com a política de privacidade',
            'politicaImagem' => 'Eu autorizo o usou de imagem, video e/ou voz'
        ];
    }

    public function scenarios(){
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['username', 'email'];

        return $scenarios;
    }

    public function getTrote(){
        return $this->hasOne(Trote::className(), ['id' => 'trote_id']);
    }

    public static function findIdentity($id){
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null){
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($username){
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByUsernameAdministrator($username){
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE, 'administrator' => 1]);
    }

    public static function findByEmail($username){
        return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByEmailAdministrator($username){
        return static::findOne(['email' => $username, 'status' => self::STATUS_ACTIVE, 'administrator' => 1]);
    }

    public static function findByPasswordResetToken($token){

        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'passwordResetToken' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token){


        if ($token == "") {
            return false;
        }


        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId(){
        return $this->id;
    }

    public function getAuthKey(){
        return $this->authKey;
    }

    public function validateAuthKey($authKey){
        return $this->authKey === $authKey;
    }

    public function validatePassword($password){
        return Yii::$app->security->validatePassword($password, $this->passwordHash);
    }

    public function setPassword($password){
        $this->passwordHash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(){
        $this->authKey = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken(){
        $this->passwordResetToken = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken(){
        $this->passwordResetToken = null;
    }

    public function requestPasswordResetToken($id){
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
    
    public function setCreated(){
        $this->created_at =  date("Y-m-d H:i:s");
    }

    public function setUpdated(){
        $this->updated_at = date("Y-m-d H:i:s");
    }
}
