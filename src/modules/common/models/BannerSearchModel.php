<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Banner;

class BannerSearchModel extends Banner{

    public function rules(){
        return [
            [['id'], 'integer'],
            [['tipo', 'img_mob', 'img_dsk'], 'safe'],
        ];
    }

    public function scenarios(){
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params){
        $query = Banner::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'tipo', $this->tipo])
            ->andFilterWhere(['like', 'img_mob', $this->img_mob])
            ->andFilterWhere(['like', 'img_dsk', $this->img_dsk]);

        return $dataProvider;
    }
}
