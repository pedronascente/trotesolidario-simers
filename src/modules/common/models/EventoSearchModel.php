<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class EventoSearchModel extends Evento
{
    public function rules()
    {
        return [
            [['id', 'trote_id'], 'integer'],
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
            'evento.id' => $this->id,
            'evento.trote_id' => $this->trote_id,
        ]);

        $query->andFilterWhere(['like', 'evento.nome', $this->nome]);

        if (!empty($this->data_evento)) {
            $query->andFilterWhere(['like', 'evento.data_evento', str_replace('T', ' ', $this->data_evento)]);
        }

        return $dataProvider;
    }
}
