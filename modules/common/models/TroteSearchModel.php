<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Trote;

class TroteSearchModel extends Trote{
    
    public function rules(){
        return [
            [['id', 'ativo'], 'integer'],
            [['nome', 'frase_certificado'], 'safe'],
        ];
    }

    public function scenarios(){
        return Model::scenarios();
    }

    public function search($params){
        $query = Trote::find();

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
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ativo' => $this->ativo,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'frase_certificado', $this->frase_certificado]);

        return $dataProvider;
    }
}
