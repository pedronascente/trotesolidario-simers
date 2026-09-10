<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Universidade;

class UniversidadeSearchModel extends Universidade
{
    public function rules()
    {
        return [
            [['id', 'ativo'], 'integer'],
            [['nome', 'cidade', 'uf', 'icon', 'link_doacao_alimento'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Universidade::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // filtros exatos
        $query->andFilterWhere([
            'id' => $this->id,
            'ativo' => $this->ativo,
        ]);

        // filtros LIKE
        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'cidade', $this->cidade])
            ->andFilterWhere(['uf' => $this->uf]) // estado melhor como filtro exato
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'link_doacao_alimento', $this->link_doacao_alimento]);

        return $dataProvider;
    }
}
