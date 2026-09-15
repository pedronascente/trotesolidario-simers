<?php

namespace app\modules\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class DistribuicaoCusto extends ActiveRecord
{
    public const SCENARIO_BATCH = 'batch';

    public static function tableName()
    {
        return '{{%distribuicao_custo}}';
    }

    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
            'value' => new Expression('NOW()'),
        ]];
    }

    public function rules()
    {
        return [
            [['categoria_custo_id', 'universidade_id', 'valor_previsto'], 'required'],
            [['categoria_custo_id', 'universidade_id'], 'integer'],
            [['valor_previsto'], 'number', 'min' => 0.01],
            [['observacao'], 'trim'],
            [['observacao'], 'string', 'max' => 500],
            [['universidade_id'], 'exist', 'targetClass' => Universidade::class, 'targetAttribute' => ['universidade_id' => 'id']],
            [['categoria_custo_id'], 'exist', 'targetClass' => CategoriaCusto::class, 'targetAttribute' => ['categoria_custo_id' => 'id']],
            [
                ['categoria_custo_id', 'universidade_id'],
                'unique',
                'targetAttribute' => ['categoria_custo_id', 'universidade_id'],
                'message' => 'A universidade ja foi adicionada a esta categoria.',
                'except' => [self::SCENARIO_BATCH],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'universidade_id' => 'Universidade / cidade',
            'valor_previsto' => 'Valor previsto',
            'observacao' => 'Observações',
        ];
    }

    public function getCategoriaCusto()
    {
        return $this->hasOne(CategoriaCusto::class, ['id' => 'categoria_custo_id']);
    }

    public function getUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'universidade_id']);
    }
}
