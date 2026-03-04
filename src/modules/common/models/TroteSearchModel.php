<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Trote;

class TroteSearchModel extends Trote{
   
    public function rules(){
        return [
            [['id', 'numero_edicao', 'ano', 'ativo'], 'integer'],
            [['titulo', 'status', 'data_inicio', 'data_fim'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Método principal de busca
     */
    public function search($params)
    {
        $query = Trote::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'ano' => SORT_DESC,
                    'numero_edicao' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filtros exatos
        $query->andFilterWhere([
            'id' => $this->id,
            'numero_edicao' => $this->numero_edicao,
            'ano' => $this->ano,
            'ativo' => $this->ativo,
            'status' => $this->status,
        ]);

        // Filtro parcial
        $query->andFilterWhere(['like', 'titulo', $this->titulo]);

        // Filtro por período
        if ($this->data_inicio) {
            $query->andFilterWhere(['>=', 'data_inicio', $this->data_inicio]);
        }

        if ($this->data_fim) {
            $query->andFilterWhere(['<=', 'data_fim', $this->data_fim]);
        }

        return $dataProvider;
    }
}
