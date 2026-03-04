<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class DocumentoSearchModel extends Documento
{
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nome', 'arquivo', 'tipo'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Documento::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'tipo' => $this->tipo,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'arquivo', $this->arquivo]);

        return $dataProvider;
    }
}
