<?php

namespace app\modules\common\models;

use yii\data\ActiveDataProvider;

class DoacaoSearchModel extends Doacao
{
    // Campos virtuais para filtro
    public $universidade_nome;
    public $trote_titulo;
    public $evento_nome;

    public function rules()
    {
        return [
            [['id', 'evento_id', 'user_id', 'tipo_doacao_id', 'universidade_id'], 'integer'],
            [['status', 'universidade_nome', 'trote_titulo', 'evento_nome', 'user_nome'], 'safe'],
        ];
    }

    public function search($params)
    {
        // Faz join com as relações para poder filtrar pelo nome/titulo
        $query = Doacao::find()
            ->joinWith(['universidade', 'trote', 'evento','user'])
            ->with(['user',  'tipoDoacao']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'status',
                    'user_id',
                    'tipo_doacao_id',
                    // sort pelos campos virtuais
                    'universidade_nome' => [
                        'asc' => ['universidade.nome' => SORT_ASC],
                        'desc' => ['universidade.nome' => SORT_DESC],
                    ],
                    'trote_titulo' => [
                        'asc' => ['trote.titulo' => SORT_ASC],
                        'desc' => ['trote.titulo' => SORT_DESC],
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

        // Filtros padrão
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'tipo_doacao_id' => $this->tipo_doacao_id,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status]);

        // Filtros pelos campos virtuais
        $query->andFilterWhere(['like', 'universidade.nome', $this->universidade_nome])
            ->andFilterWhere(['like', 'trote.titulo', $this->trote_titulo])
            ->andFilterWhere(['like', 'evento.nome', $this->evento_nome]);

        return $dataProvider;
    }
}
