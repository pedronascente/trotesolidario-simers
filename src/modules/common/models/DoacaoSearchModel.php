<?php

namespace app\modules\common\models;

use yii\data\ActiveDataProvider;

class DoacaoSearchModel extends Doacao
{
    public function rules()
    {
        return [
            [['id', 'participacao_id', 'tipo_doacao_id', 'evento_id'], 'integer'],
            [['status', 'participacao_label', 'evento_nome', 'tipo_doacao_nome', 'cpf_snapshot', 'edicao_snapshot'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Doacao::find()
            ->joinWith(['participacao.user', 'participacao.trote', 'participacao.universidade', 'evento', 'tipoDoacao'])
            ->with(['participacao.user', 'participacao.trote', 'participacao.universidade', 'evento', 'tipoDoacao', 'validador']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'status',
                    'cpf_snapshot',
                    'edicao_snapshot',
                    'participacao_label' => [
                        'asc' => ['user.nome' => SORT_ASC],
                        'desc' => ['user.nome' => SORT_DESC],
                    ],
                    'tipo_doacao_nome' => [
                        'asc' => ['tipo_doacao.nome' => SORT_ASC],
                        'desc' => ['tipo_doacao.nome' => SORT_DESC],
                    ],
                    'evento_nome' => [
                        'asc' => ['evento.nome' => SORT_ASC],
                        'desc' => ['evento.nome' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'doacao.id' => $this->id,
            'doacao.participacao_id' => $this->participacao_id,
            'doacao.tipo_doacao_id' => $this->tipo_doacao_id,
            'doacao.evento_id' => $this->evento_id,
        ]);

        $query->andFilterWhere(['like', 'doacao.status', $this->status])
            ->andFilterWhere(['like', 'doacao.cpf_snapshot', $this->cpf_snapshot])
            ->andFilterWhere(['like', 'doacao.edicao_snapshot', $this->edicao_snapshot])
            ->andFilterWhere(['like', 'user.nome', $this->participacao_label])
            ->andFilterWhere(['like', 'evento.nome', $this->evento_nome])
            ->andFilterWhere(['like', 'tipo_doacao.nome', $this->tipo_doacao_nome]);

        return $dataProvider;
    }
}
