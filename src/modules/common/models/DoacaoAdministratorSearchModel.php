<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Doacao;
use app\modules\common\models\Helper;

class DoacaoAdministratorSearchModel extends Doacao
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'usuario_validacao', 'ativo', 'user_create', 'trote_id', 'user_update'], 'integer'],
            [['arquivo', 'instituicao', 'validado', 'validado_motivo',  'tipo_doacao', 'data_create', 'data_update'], 'safe'],
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
        if ($this->trote_id) {
            $query->andWhere("trote_id = '$this->trote_id'");
        }
        if ($this->user_create) {
            $query->andWhere("user_create = '$this->user_create'");
        }

        if ($this->validado === '1') {
            $query->andWhere("validado = '$this->validado'");
        }
        if ($this->validado === '0') {
            $query->andWhere("validado = '$this->validado' OR validado is null");
        }

        if ($this->ativo === '1') {
            $query->andWhere("ativo = '$this->ativo'");
        } elseif ($this->ativo === '0') {
            $query->andWhere("ativo = '$this->ativo'");
        } else {
            $query->andWhere("ativo IN ('0','1')");
        }

        return $dataProvider;
    }
}
