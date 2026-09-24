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
            [['trote_id', 'mercado_id', 'universidade_id'], 'required'],
            [['trote_id', 'mercado_id', 'universidade_id'], 'integer'],
            [['mercado_id', 'universidade_id', 'trote_id'], 'unique', 'targetAttribute' => ['mercado_id', 'universidade_id', 'trote_id'], 'message' => 'Este mercado já está vinculado a esta universidade no trote selecionado.'],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::class, 'targetAttribute' => ['trote_id' => 'id']],
            ['trote_id', 'validateTroteAtivo', 'skipOnError' => true],
            [['mercado_id'], 'exist', 'skipOnError' => true, 'targetClass' => MercadoParceiro::class, 'targetAttribute' => ['mercado_id' => 'id']],
            [['universidade_id'], 'exist', 'skipOnError' => true, 'targetClass' => Universidade::class, 'targetAttribute' => ['universidade_id' => 'id']],
            [['created_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trote_id' => 'Trote',
            'mercado_id' => 'Mercado',
            'universidade_id' => 'Universidade',
            'created_at' => 'Criado em',
        ];
    }

    public function validateTroteAtivo($attribute): void
    {
        $troteAtivoExiste = Trote::find()
            ->where([
                'id' => $this->$attribute,
                'status' => Trote::STATUS_ATIVO,
            ])
            ->exists();

        if (!$troteAtivoExiste) {
            $this->addError($attribute, 'Selecione um trote ativo.');
        }
    }

    public function getMercado()
    {
        return $this->hasOne(MercadoParceiro::class, ['id' => 'mercado_id']);
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }

    public function getUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'universidade_id']);
    }
}
