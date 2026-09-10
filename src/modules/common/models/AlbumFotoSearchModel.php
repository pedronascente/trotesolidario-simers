<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class AlbumFotoSearchModel extends AlbumFoto
{
    public $participante;
    public $trote;

    public function rules()
    {
        return [
            [['id', 'participacao_id'], 'integer'],
            [['titulo', 'participante', 'trote'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = AlbumFoto::find()
            ->alias('af')
            ->joinWith(['participacao p', 'participacao.user u', 'participacao.trote t']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC, 'id' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        $dataProvider->sort->attributes['participante'] = ['asc' => ['u.nome' => SORT_ASC], 'desc' => ['u.nome' => SORT_DESC]];
        $dataProvider->sort->attributes['trote'] = ['asc' => ['t.edicao' => SORT_ASC], 'desc' => ['t.edicao' => SORT_DESC]];

        if (!$this->load($params) || !$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['af.id' => $this->id, 'af.participacao_id' => $this->participacao_id])
            ->andFilterWhere(['like', 'af.titulo', $this->titulo])
            ->andFilterWhere(['like', 'u.nome', $this->participante])
            ->andFilterWhere(['like', 't.edicao', $this->trote]);

        return $dataProvider;
    }
}
