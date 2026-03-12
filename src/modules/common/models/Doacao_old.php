<?php

namespace app\modules\common\models;

use Yii;

class Doacao extends \yii\db\ActiveRecord{

    public $file;
    public $user_create_email;
    public $comprovante;

    public static function tableName(){
        return '_doacao';
    }

    public function rules(){
        return [
            [['arquivo', 'instituicao', 'trote_id', 'tipo_doacao'], 'required'],
            [['arquivo', 'tipo_doacao', 'validado_motivo'], 'string'],
            [['trote_id', 'validado', 'instituicao',  'usuario_validacao', 'user_create', 'user_update', 'ativo'], 'integer'],
            [['data_create', 'data_update'], 'safe'],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::className(), 'targetAttribute' => ['trote_id' => 'id']],
            [['user_create'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_create' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_update' => 'id']],
            [['usuario_validacao'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['usuario_validacao' => 'id']],
        ];
    }

    
    public function attributeLabels(){
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

    public function getTrote(){
        return $this->hasOne(Trote::className(), ['id' => 'trote_id']);
    }
   
    public function getUserCreate(){
        return $this->hasOne(Users::className(), ['id' => 'user_create']);
    }

    public function getUserUpdate(){
        return $this->hasOne(Users::className(), ['id' => 'user_update']);
    }

    public function getUsuarioValidacao(){
        return $this->hasOne(Users::className(), ['id' => 'usuario_validacao']);
    }
    public function getUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'instituicao']);
    }
}
