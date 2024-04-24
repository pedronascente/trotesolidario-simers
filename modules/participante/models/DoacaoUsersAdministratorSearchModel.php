<?php

namespace app\modules\participante\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\participante\models\Doacao;
use app\modules\participante\models\Helper;

/**
 * DoacaoUsersAdministratorSearchModel represents the model behind the search form of `app\modules\participante\models\Doacao`.
 */
class DoacaoUsersAdministratorSearchModel extends Doacao
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'usuario_validacao',], 'integer'],
            [['arquivo', 'instituicao', 'validado', 'trote', 'tipo_doacao', 'data_create', 'data_update', 'ativo', 'user_create', 'user_update', 'user_create_email'], 'safe'],
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


        //Helper::d($params);
        if ($this->user_create_email) {

            $query->andWhere(["user_create" => $this->user_create_email]);
        }
        if ($this->user_create) {
            $query->andWhere(["user_create" => $this->user_create]);
        }
        if ($this->tipo_doacao) {
            $query->andWhere(["tipo_doacao" => $this->tipo_doacao]);
        }
        if ($this->instituicao) {
            $query->andWhere(["instituicao" => $this->instituicao]);
        }
        if ($this->trote) {
            $query->andWhere(["trote" => $this->trote]);
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
