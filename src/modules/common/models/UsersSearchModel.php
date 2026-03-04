<?php

namespace app\modules\common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\common\models\Users;

class UsersSearchModel extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['name', 'cpf', 'estudante', 'telefone', 'conheceONas', 'previsaoFormatura', 'instituicao', 'outraInstituicao', 'passwordHash', 'email', 'created_at', 'updated_at', 'username', 'passwordResetToken', 'authKey', 'trote_id'], 'safe'],
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
        $query = Users::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'passwordHash', $this->passwordHash])
            ->andFilterWhere(['like', 'cpf', $this->cpf])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'estudante', $this->estudante])
            ->andFilterWhere(['like', 'instituicao', $this->instituicao])
            ->andFilterWhere(['like', 'trote_id', $this->trote_id])
            ->andFilterWhere(['like', 'outraInstituicao', $this->outraInstituicao])
            ->andFilterWhere(['like', 'previsaoFormatura', $this->previsaoFormatura])
            ->andFilterWhere(['like', 'conheceONas', $this->conheceONas])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'passwordResetToken', $this->passwordResetToken])
            ->andFilterWhere(['like', 'authKey', $this->authKey]);

        return $dataProvider;
    }
}
