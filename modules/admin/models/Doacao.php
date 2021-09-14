<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "_doacao".
 *
 * @property int $id
 * @property string|null $arquivo
 * @property string|null $instituicao
 * @property string|null $validado
 * @property string|null $trote
 * @property int|null $usuario_validacao
 * @property string|null $tipo_doacao
 * @property int|null $user_create
 * @property string|null $data_create
 * @property int|null $user_update
 * @property string|null $data_update
 *
 * @property Users $userCreate
 * @property Users $userUpdate
 * @property Users $usuarioValidacao
 */
class Doacao extends \yii\db\ActiveRecord {

    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '_doacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['arquivo', 'instituicao', 'tipo_doacao', 'trote'], 'required'],
            [['arquivo', 'instituicao', 'validado', 'tipo_doacao', 'trote'], 'string'],
            [['usuario_validacao', 'user_create', 'user_update', 'ativo'], 'integer'],
            [['data_create', 'data_update'], 'safe'],
            [['user_create'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_create' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['usuario_validacao'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['usuario_validacao' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'arquivo' => 'Arquivo',
            'instituicao' => 'Instituição',
            'validado' => 'Validado',
            'usuario_validacao' => 'Usuario Validacao',
            'tipo_doacao' => 'Tipo Doação',
            'user_create' => 'User Create',
            'data_create' => 'Data Create',
            'user_update' => 'User Update',
            'data_update' => 'Data Update',
            'ativo' => 'Ativo',
            'trote' => 'Evento'
        ];
    }

    /**
     * Gets query for [[UserCreate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreate() {
        return $this->hasOne(Users::className(), ['id' => 'user_create']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate() {
        return $this->hasOne(Users::className(), ['id' => 'user_update']);
    }

    /**
     * Gets query for [[UsuarioValidacao]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioValidacao() {
        return $this->hasOne(Users::className(), ['id' => 'usuario_validacao']);
    }

}
