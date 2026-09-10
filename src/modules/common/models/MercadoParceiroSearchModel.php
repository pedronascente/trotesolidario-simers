<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class MercadoParceiroSearchModel extends MercadoParceiro
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nome_mercado', 'endereco', 'numero', 'bairro'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = MercadoParceiro::find()->alias('mp');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
            'sort' => ['defaultOrder' => ['nome_mercado' => SORT_ASC]],
        ]);

        if (!$this->load($params) || !$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['mp.id' => $this->id])
            ->andFilterWhere(['like', 'mp.nome_mercado', $this->nome_mercado])
            ->andFilterWhere(['like', 'mp.endereco', $this->endereco])
            ->andFilterWhere(['like', 'mp.numero', $this->numero])
            ->andFilterWhere(['like', 'mp.bairro', $this->bairro]);

        return $dataProvider;
    }
}
