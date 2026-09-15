<?php

namespace app\modules\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class TipoCategoriaCusto extends ActiveRecord
{
    public static function tableName() { return '{{%tipo_categoria_custo}}'; }

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
            [['nome'], 'required'],
            [['nome', 'descricao'], 'trim'],
            [['nome'], 'string', 'max' => 120],
            [['descricao'], 'string', 'max' => 500],
            [['ativo'], 'boolean'],
            [['ativo'], 'default', 'value' => 1],
            [['nome'], 'unique', 'message' => 'Ja existe uma categoria com este nome.'],
        ];
    }

    public function attributeLabels()
    {
        return ['nome' => 'Nome', 'descricao' => 'Descrição', 'ativo' => 'Ativa'];
    }

    public function getCustos()
    {
        return $this->hasMany(CategoriaCusto::class, ['tipo_categoria_custo_id' => 'id']);
    }
}
