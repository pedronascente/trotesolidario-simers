<?php

namespace app\modules\common\models;

use yii\db\ActiveRecord;

class MercadoUniversidade extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%mercado_universidade}}';
    }

    public function rules()
    {
        return [
            [['mercado_id', 'universidade_id'], 'required'],
            [['mercado_id', 'universidade_id'], 'integer'],
            [['mercado_id', 'universidade_id'], 'unique', 'targetAttribute' => ['mercado_id', 'universidade_id'], 'message' => 'Este mercado já está vinculado a esta universidade.'],
            [['mercado_id'], 'exist', 'skipOnError' => true, 'targetClass' => MercadoParceiro::class, 'targetAttribute' => ['mercado_id' => 'id']],
            [['universidade_id'], 'exist', 'skipOnError' => true, 'targetClass' => Universidade::class, 'targetAttribute' => ['universidade_id' => 'id']],
            [['created_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mercado_id' => 'Mercado',
            'universidade_id' => 'Universidade',
            'created_at' => 'Criado em',
        ];
    }

    public function getMercado()
    {
        return $this->hasOne(MercadoParceiro::class, ['id' => 'mercado_id']);
    }

    public function getUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'universidade_id']);
    }
}
