<?php

namespace app\modules\common\models;

use yii\db\ActiveRecord;

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
            [['trote_id'], 'integer'],
            [['data_evento'], 'datetime', 'format' => 'php:Y-m-d\TH:i'],
            [['nome'], 'string', 'max' => 150],
            [
                ['trote_id'],
                'exist',
                'targetClass' => Trote::class,
                'targetAttribute' => ['trote_id' => 'id'],
            ],
            ['trote_id', 'validateTroteAberto', 'skipOnError' => true],
        ];
    }

    public function validateTroteAberto($attribute): void
    {
        if (!$this->isNewRecord) {
            return;
        }

        $trote = Trote::findOne($this->$attribute);
        if ($trote !== null && $trote->status === Trote::STATUS_ENCERRADO) {
            $this->addError($attribute, 'Nao e possivel criar eventos para um trote encerrado.');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trote_id' => 'Trote',
            'nome' => 'Nome',
            'data_evento' => 'Data do evento',
        ];
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if (!empty($this->data_evento)) {
            $timestamp = strtotime($this->data_evento);
            if ($timestamp !== false) {
                $this->data_evento = date('Y-m-d\TH:i', $timestamp);
            }
        }

        return true;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!empty($this->data_evento)) {
            $timestamp = strtotime($this->data_evento);
            if ($timestamp !== false) {
                $this->data_evento = date('Y-m-d H:i:s', $timestamp);
            }
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!empty($this->data_evento)) {
            $timestamp = strtotime($this->data_evento);
            if ($timestamp !== false) {
                $this->data_evento = date('Y-m-d\TH:i', $timestamp);
            }
        }
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }
}
