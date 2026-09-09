<?php

namespace app\modules\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class CidadeParticipante extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%cidade_participante}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['cidade', 'uf'], 'required'],
            [['cidade', 'uf'], 'trim'],
            ['cidade', 'string', 'max' => 255],
            ['uf', 'string', 'length' => 2],
            ['uf', 'filter', 'filter' => 'strtoupper'],
            [['cidade', 'uf'], 'unique', 'targetAttribute' => ['cidade', 'uf'], 'message' => 'Esta cidade já está cadastrada para a UF informada.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'cidade' => 'Cidade',
            'uf' => 'UF',
        ];
    }
}
