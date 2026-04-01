<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TroteSearchModel extends Trote
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['titulo', 'edicao', 'status', 'data_inicio', 'data_fim'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

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
                    'edicao' => SORT_DESC,
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'titulo', $this->titulo]);
        $query->andFilterWhere(['like', 'edicao', $this->edicao]);

        if ($this->data_inicio) {
            $query->andFilterWhere(['>=', 'data_inicio', $this->data_inicio]);
        }

        if ($this->data_fim) {
            $query->andFilterWhere(['<=', 'data_fim', $this->data_fim]);
        }

        return $dataProvider;
    }
}
