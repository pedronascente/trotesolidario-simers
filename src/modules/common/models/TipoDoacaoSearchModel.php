<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TipoDoacaoSearchModel extends TipoDoacao
{
    /**
     * Regras de validação para os filtros
     */
    public function rules()
    {
        return [
            [['id', 'carga_horaria', 'pontuacao_ranking', 'ativo'], 'integer'],
            [['nome', 'descricao'], 'safe'],
        ];
    }

    /**
     * Cria o ActiveDataProvider com filtros aplicados
     */
    public function search($params)
    {
        $query = TipoDoacao::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // retorna sem filtrar se inválido
            return $dataProvider;
        }

        // filtros
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['carga_horaria' => $this->carga_horaria]);
        $query->andFilterWhere(['pontuacao_ranking' => $this->pontuacao_ranking]);
        $query->andFilterWhere(['ativo' => $this->ativo]);
        $query->andFilterWhere(['like', 'nome', $this->nome]);
        $query->andFilterWhere(['like', 'descricao', $this->descricao]);

        return $dataProvider;
    }
}
