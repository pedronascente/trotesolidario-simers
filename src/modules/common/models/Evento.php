<?php

namespace app\modules\common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Evento extends ActiveRecord
{
    public static function tableName()
    {
        return 'evento';
    }

    public function rules()
    {
        return [
            [['trote_id', 'nome', 'data_evento'], 'required'],
            [['trote_id', 'ativo'], 'integer'],
            [['descricao'], 'string'],
            [['data_evento', 'created_at', 'updated_at'], 'safe'],
            [['nome'], 'string', 'max' => 255],
            [
                'trote_id',
                'exist',
                'targetClass' => Trote::class,
                'targetAttribute' => ['trote_id' => 'id'],
                'filter' => ['ativo' => 1],
                'message' => 'Trote inválido ou inativo.',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trote_id' => 'Trote',
            'nome' => 'Evento',
            'descricao' => 'Descrição',
            'ativo' => 'Ativo',
            'data_evento' => 'Data do Evento',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }
    
}
