<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ComissaoOrganizadoraSearchModel extends ComissaoOrganizadora
{
    public function rules()
    {
        return [
            [['id', 'universidade_id', 'ordem', 'ativo'], 'integer'],
            [['nome', 'cargo'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = ComissaoOrganizadora::find()
            ->alias('co')
            ->joinWith(['universidade un']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['ordem' => SORT_ASC, 'id' => SORT_ASC]],
            'pagination' => ['pageSize' => 20],
        ]);

        if (!$this->load($params) || !$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'co.id' => $this->id,
            'co.universidade_id' => $this->universidade_id,
            'co.ordem' => $this->ordem,
            'co.ativo' => $this->ativo,
        ])
            ->andFilterWhere(['like', 'co.nome', $this->nome])
            ->andFilterWhere(['like', 'co.cargo', $this->cargo]);

        return $dataProvider;
    }
}