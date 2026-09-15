<?php

namespace app\modules\common\models;

use yii\data\ActiveDataProvider;

class TipoCategoriaCustoSearchModel extends TipoCategoriaCusto
{
    public function rules() { return [[['nome'], 'safe'], [['ativo'], 'integer']]; }

    public function search(array $params): ActiveDataProvider
    {
        $query = TipoCategoriaCusto::find()->orderBy(['nome' => SORT_ASC]);
        $provider = new ActiveDataProvider(['query' => $query]);
        if ($this->load($params) && $this->validate()) {
            $query->andFilterWhere(['ativo' => $this->ativo])->andFilterWhere(['like', 'nome', $this->nome]);
        }
        return $provider;
    }
}