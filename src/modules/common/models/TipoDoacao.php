<?php

namespace app\modules\common\models;

use Yii;
use yii\db\ActiveRecord;

class TipoDoacao extends ActiveRecord
{
    public static function tableName()
    {
        return 'tipo_doacao';
    }

    public function rules()
    {
        return [
            [['nome', 'carga_horaria', 'pontuacao_ranking'], 'required'],
            [['nome'], 'unique', 'message' => 'Este nome já se encontra registrado!'],
            [['nome'], 'string', 'max' => 150],
            [['descricao'], 'string', 'max' => 255],

            [['carga_horaria', 'pontuacao_ranking', 'ativo'], 'integer'],
            [['carga_horaria'], 'integer', 'max' => 1000],

            // 🔒 REGRA DE NEGÓCIO: NÃO PERMITIR VALORES NEGATIVOS
            [
                ['carga_horaria', 'pontuacao_ranking'],
                'number',
                'min' => 0,
                'tooSmall' => '{attribute} não pode ser negativo.'
            ],

            [['ativo'], 'default', 'value' => 1],
           
        ];
    }

    public function beforeValidate()
    {
        if ($this->nome) {
            $this->nome = trim($this->nome);
        }
        return parent::beforeValidate();
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'carga_horaria' => 'Carga Horária',
            'pontuacao_ranking' => 'Pontuação Ranking',
            'ativo' => 'Ativo',
        ];
    }
}
