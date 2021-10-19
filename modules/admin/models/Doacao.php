<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "_doacao".
 *
 * @property int $id
 * @property string $arquivo
 * @property string $instituicao
 * @property int $trote_id
 * @property string $tipo_doacao
 * @property int|null $validado
 * @property string|null $validado_motivo
 * @property int|null $usuario_validacao
 * @property int|null $user_create
 * @property string|null $data_create
 * @property int|null $user_update
 * @property string|null $data_update
 * @property int|null $ativo
 *
 * @property Trote $trote
 * @property Users $userCreate
 * @property Users $userUpdate
 * @property Users $usuarioValidacao
 */
class Doacao extends \yii\db\ActiveRecord {

    public $file;
    public $user_create_email;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_doacao';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['arquivo', 'instituicao', 'trote_id', 'tipo_doacao'], 'required'],
            [['arquivo', 'instituicao', 'tipo_doacao', 'validado_motivo'], 'string'],
            [['trote_id', 'validado', 'usuario_validacao', 'user_create', 'user_update', 'ativo'], 'integer'],
            [['data_create', 'data_update'], 'safe'],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::className(), 'targetAttribute' => ['trote_id' => 'id']],
            [['user_create'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_create' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['usuario_validacao'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['usuario_validacao' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'arquivo' => 'Arquivo',
            'instituicao' => 'Instituicao',
            'trote_id' => 'Trote ID',
            'tipo_doacao' => 'Tipo Doacao',
            'validado' => 'Validado',
            'validado_motivo' => 'Validado Motivo',
            'usuario_validacao' => 'Usuario Validacao',
            'user_create' => 'User Create',
            'data_create' => 'Data Create',
            'user_update' => 'User Update',
            'data_update' => 'Data Update',
            'ativo' => 'Ativo',
        ];
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
     * Gets query for [[UserCreate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreate()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_create']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_update']);
    }

    /**
     * Gets query for [[UsuarioValidacao]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioValidacao()
    {
        return $this->hasOne(Users::className(), ['id' => 'usuario_validacao']);
    }
}
