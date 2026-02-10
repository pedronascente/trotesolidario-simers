<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Informativo;

class InformativoSearchModel extends Informativo{
    
    public function rules(){
        return [
            [['id'], 'integer'],
            [['nome', 'arquivo'], 'safe'],
        ];
    }

    
    public function scenarios(){
        return Model::scenarios();
    }

    public function search($params){
        $query = Informativo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'arquivo', $this->arquivo]);

        return $dataProvider;
    }
}
