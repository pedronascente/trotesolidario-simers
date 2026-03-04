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
            [['nome'], 'string', 'max' => 150],
            [['descricao'], 'string', 'max' => 255],
            [['carga_horaria', 'pontuacao_ranking', 'ativo'], 'integer'],
            [['ativo'], 'default', 'value' => 1], // assume ativo por padrão
            [['nome'], 'unique'], // se quiser nome único
        ];
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
