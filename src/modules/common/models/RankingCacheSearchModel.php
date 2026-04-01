<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class RankingCacheSearchModel extends RankingCache
{
    public $user_nome;
    public $universidade_nome;
    public $trote_edicao;

    public function rules()
    {
        return [
            [['id', 'participacao_id', 'trote_id', 'pontuacao_total', 'posicao'], 'integer'],
            [['updated_at', 'user_nome', 'universidade_nome', 'trote_edicao'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = RankingCache::find()->joinWith(['participacao.user', 'participacao.universidade', 'trote']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['trote_id' => SORT_DESC, 'posicao' => SORT_ASC, 'pontuacao_total' => SORT_DESC],
                'attributes' => [
                    'id' => [
                        'asc' => ['ranking_cache.id' => SORT_ASC],
                        'desc' => ['ranking_cache.id' => SORT_DESC],
                    ],
                    'participacao_id' => [
                        'asc' => ['ranking_cache.participacao_id' => SORT_ASC],
                        'desc' => ['ranking_cache.participacao_id' => SORT_DESC],
                    ],
                    'trote_id' => [
                        'asc' => ['ranking_cache.trote_id' => SORT_ASC],
                        'desc' => ['ranking_cache.trote_id' => SORT_DESC],
                    ],
                    'pontuacao_total' => [
                        'asc' => ['ranking_cache.pontuacao_total' => SORT_ASC],
                        'desc' => ['ranking_cache.pontuacao_total' => SORT_DESC],
                    ],
                    'posicao' => [
                        'asc' => ['ranking_cache.posicao' => SORT_ASC],
                        'desc' => ['ranking_cache.posicao' => SORT_DESC],
                    ],
                    'updated_at' => [
                        'asc' => ['ranking_cache.updated_at' => SORT_ASC],
                        'desc' => ['ranking_cache.updated_at' => SORT_DESC],
                    ],
                    'user_nome' => [
                        'asc' => ['user.nome' => SORT_ASC],
                        'desc' => ['user.nome' => SORT_DESC],
                    ],
                    'universidade_nome' => [
                        'asc' => ['universidade.nome' => SORT_ASC],
                        'desc' => ['universidade.nome' => SORT_DESC],
                    ],
                    'trote_edicao' => [
                        'asc' => ['trote.edicao' => SORT_ASC],
                        'desc' => ['trote.edicao' => SORT_DESC],
                    ],
                ],
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
            'ranking_cache.id' => $this->id,
            'ranking_cache.participacao_id' => $this->participacao_id,
            'ranking_cache.trote_id' => $this->trote_id,
            'ranking_cache.pontuacao_total' => $this->pontuacao_total,
            'ranking_cache.posicao' => $this->posicao,
        ]);

        $query->andFilterWhere(['like', 'ranking_cache.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'user.nome', $this->user_nome])
            ->andFilterWhere(['like', 'universidade.nome', $this->universidade_nome])
            ->andFilterWhere(['like', 'trote.edicao', $this->trote_edicao]);

        return $dataProvider;
    }
}
