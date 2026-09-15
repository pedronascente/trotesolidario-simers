<?php

namespace app\modules\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class CategoriaCusto extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%categoria_custo}}';
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
            [['trote_id', 'tipo_categoria_custo_id', 'valor_previsto', 'descricao'], 'required'],
            [['trote_id', 'tipo_categoria_custo_id'], 'integer'],
            [['valor_previsto'], 'number', 'min' => 0.01],
            [['descricao', 'observacao'], 'trim'],
            [['descricao', 'observacao'], 'string', 'max' => 500],
            [['trote_id'], 'exist', 'targetClass' => Trote::class, 'targetAttribute' => ['trote_id' => 'id']],
            [['tipo_categoria_custo_id'], 'exist', 'targetClass' => TipoCategoriaCusto::class, 'targetAttribute' => ['tipo_categoria_custo_id' => 'id']],
            [['trote_id', 'tipo_categoria_custo_id'], 'unique', 'targetAttribute' => ['trote_id', 'tipo_categoria_custo_id'], 'message' => 'Esta categoria ja existe para o trote selecionado.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'trote_id' => 'Trote / edição',
            'tipo_categoria_custo_id' => 'Categoria do custo',
            'valor_previsto' => 'Valor total previsto',
            'descricao' => 'Descrição',
            'observacao' => 'Observações',
        ];
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }

    public function getTipoCategoriaCusto()
    {
        return $this->hasOne(TipoCategoriaCusto::class, ['id' => 'tipo_categoria_custo_id']);
    }

    public function getDistribuicoes()
    {
        return $this->hasMany(DistribuicaoCusto::class, ['categoria_custo_id' => 'id']);
    }
}
