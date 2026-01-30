<?php

namespace app\modules\participante\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\participante\models\Universidade;

/**
 * UniversidadeSearchModel represents the model behind the search form of `app\modules\participante\models\Universidade`.
 */
class UniversidadeSearchModel extends Universidade
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'trote_id', 'ativo'], 'integer'],
            [['nome', 'icon', 'link_doacao_alimento'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Universidade::find();

        // add conditions that should always apply here

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
            'trote_id' => $this->trote_id,
            'ativo' => $this->ativo,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'icon', $this->icon])
            ->andFilterWhere(['like', 'link_doacao_alimento', $this->link_doacao_alimento]);

        return $dataProvider;
    }

    
}
