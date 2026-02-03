<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Universidade;

class UniversidadeSearchModel extends Universidade{

    public function rules(){
        return [
            [['id', 'trote_id', 'ativo'], 'integer'],
            [['nome', 'icon', 'link_doacao_alimento'], 'safe'],
        ];
    }

    public function scenarios(){
        return Model::scenarios();
    }

    public function search($params){
        $query = Universidade::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'trote_id' => $this->trote_id,
            'ativo' => $this->ativo,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'link_doacao_alimento', $this->link_doacao_alimento]);

        return $dataProvider;
    }    
}
