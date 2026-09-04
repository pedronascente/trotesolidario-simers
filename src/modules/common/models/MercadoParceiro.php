<?php

namespace app\modules\common\models;
use yii\db\ActiveRecord;

class MercadoParceiro extends ActiveRecord
{
     public static function tableName()
    {
        return '{{%mercado_parceiro}}';
    }

    public function rules()
    {
        return [
            [['nome_mercado', 'endereco', 'numero', 'bairro'], 'required'],

            [['nome_mercado', 'endereco'], 'string', 'max' => 255],

            [['numero'], 'string', 'max' => 20],

            [['bairro'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome_mercado' => 'Nome do Mercado',
            'endereco' => 'Endereço',
            'numero' => 'Número',
            'bairro' => 'Bairro',
        ];
    }
}