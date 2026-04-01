<?php

namespace app\modules\common\models;

use yii\db\ActiveRecord;

class RankingCache extends ActiveRecord
{
    public static function tableName()
    {
        return 'ranking_cache';
    }

    public function rules()
    {
        return [
            [['participacao_id', 'trote_id', 'pontuacao_total', 'updated_at'], 'required'],
            [['participacao_id', 'trote_id', 'pontuacao_total', 'posicao'], 'integer'],
            [['updated_at'], 'safe'],
            [['participacao_id'], 'unique'],
            [['participacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Participacao::class, 'targetAttribute' => ['participacao_id' => 'id']],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::class, 'targetAttribute' => ['trote_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participacao_id' => 'Participacao',
            'trote_id' => 'Trote',
            'pontuacao_total' => 'Pontuacao total',
            'posicao' => 'Posicao',
            'updated_at' => 'Atualizado em',
        ];
    }

    public function getParticipacao()
    {
        return $this->hasOne(Participacao::class, ['id' => 'participacao_id']);
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }
}
