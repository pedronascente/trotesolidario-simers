<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ParticipacaoSearchModel extends Participacao
{
    public $user_nome;
    public $user_email;
    public $trote_edicao;
    public $universidade_nome;

    public function rules()
    {
        return [
            [['id', 'user_id', 'trote_id', 'universidade_id'], 'integer'],
            [['curso', 'status', 'created_at', 'updated_at', 'user_nome', 'user_email', 'trote_edicao', 'universidade_nome'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Participacao::find()->joinWith(['user', 'trote', 'universidade']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id' => [
                        'asc' => ['participacao.id' => SORT_ASC],
                        'desc' => ['participacao.id' => SORT_DESC],
                    ],
                    'curso' => [
                        'asc' => ['participacao.curso' => SORT_ASC],
                        'desc' => ['participacao.curso' => SORT_DESC],
                    ],
                    'status' => [
                        'asc' => ['participacao.status' => SORT_ASC],
                        'desc' => ['participacao.status' => SORT_DESC],
                    ],
                    'created_at' => [
                        'asc' => ['participacao.created_at' => SORT_ASC],
                        'desc' => ['participacao.created_at' => SORT_DESC],
                    ],
                    'updated_at' => [
                        'asc' => ['participacao.updated_at' => SORT_ASC],
                        'desc' => ['participacao.updated_at' => SORT_DESC],
                    ],
                    'user_nome' => [
                        'asc' => ['user.nome' => SORT_ASC],
                        'desc' => ['user.nome' => SORT_DESC],
                    ],
                    'user_email' => [
                        'asc' => ['user.email' => SORT_ASC],
                        'desc' => ['user.email' => SORT_DESC],
                    ],
                    'trote_edicao' => [
                        'asc' => ['trote.edicao' => SORT_ASC],
                        'desc' => ['trote.edicao' => SORT_DESC],
                    ],
                    'universidade_nome' => [
                        'asc' => ['universidade.nome' => SORT_ASC],
                        'desc' => ['universidade.nome' => SORT_DESC],
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
            'participacao.id' => $this->id,
            'participacao.user_id' => $this->user_id,
            'participacao.trote_id' => $this->trote_id,
            'participacao.universidade_id' => $this->universidade_id,
            'participacao.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'participacao.curso', $this->curso])
            ->andFilterWhere(['like', 'participacao.created_at', $this->created_at])
            ->andFilterWhere(['like', 'participacao.updated_at', $this->updated_at])
            ->andFilterWhere(['like', 'user.nome', $this->user_nome])
            ->andFilterWhere(['like', 'user.email', $this->user_email])
            ->andFilterWhere(['like', 'trote.edicao', $this->trote_edicao])
            ->andFilterWhere(['like', 'universidade.nome', $this->universidade_nome]);

        return $dataProvider;
    }
}
