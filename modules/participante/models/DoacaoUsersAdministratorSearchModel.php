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
            [['arquivo', 'instituicao', 'validado', 'trote_id', 'trote', 'tipo_doacao', 'data_create', 'data_update', 'ativo', 'user_create', 'user_update', 'user_create_email'], 'safe'],
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

        $pageSize = isset($params['per-page']) ? (int) $params['per-page'] : 12;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
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
        if ($this->trote_id) {
            $query->andWhere(["trote_id" => $this->trote_id]);
        }


        if ($this->validado === 1) {
            $query->andWhere("validado = 1");
        } elseif ($this->validado === '0') {
            $query->andWhere("validado = 0");
        } elseif ($this->validado == 2 || $this->validado == null) {

            $query->andWhere("validado IS NULL");
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
