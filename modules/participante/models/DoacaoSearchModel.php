<?php

namespace app\modules\participante\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\participante\models\Doacao;
use app\modules\participante\models\Helper;

/**
 * DoacaoSearchModel represents the model behind the search form of `app\modules\participante\models\Doacao`.
 */
class DoacaoSearchModel extends Doacao
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'usuario_validacao', 'ativo', 'user_create', 'user_update'], 'integer'],
            [['arquivo', 'instituicao', 'validado', 'trote', 'tipo_doacao', 'data_create', 'data_update'], 'safe'],
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
        $query = Doacao::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if ($this->tipo_doacao) {
            $query->andWhere("tipo_doacao = '$this->tipo_doacao'");
        }
        if ($this->instituicao) {
            $query->andWhere("instituicao = '$this->instituicao'");
        }
        if ($this->trote) {
            $query->andWhere("trote = '$this->trote'");
        }

        if ($this->ativo === '1') {
            $query->andWhere("ativo = '$this->ativo'");
        } elseif ($this->ativo === '0') {
            $query->andWhere("ativo = '$this->ativo'");
        } else {
            $query->andWhere("ativo IN ('0','1')");
        }

        $query->andWhere("user_create = " . \Yii::$app->user->identity->id . " OR user_update = " . \Yii::$app->user->identity->id);
        return $dataProvider;
    }
}
