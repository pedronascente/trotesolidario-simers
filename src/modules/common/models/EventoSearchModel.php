<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class EventoSearchModel extends Evento
{
    public function rules()
    {
        return [
            [['id', 'trote_id', 'ativo'], 'integer'],
            [['nome', 'data_evento'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Evento::find()->joinWith(['trote']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['data_evento' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'trote_id' => $this->trote_id,
            'ativo' => $this->ativo,
            'data_evento' => $this->data_evento,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome]);

        return $dataProvider;
    }
}
