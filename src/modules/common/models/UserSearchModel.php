<?php

namespace app\modules\common\models;

use app\models\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearchModel extends User
{
    public $has_participante;
    public $estudante;
    public $estudante_medicina;

    public function rules()
    {
        return [
            [['id', 'status', 'has_participante', 'estudante', 'estudante_medicina'], 'integer'],
            [['nome', 'email', 'username', 'cpf', 'role', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = User::find()->joinWith(['participante']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'nome',
                    'email',
                    'username',
                    'cpf',
                    'role',
                    'status',
                    'created_at',
                    'updated_at',
                    'has_participante' => [
                        'asc' => ['participante.id' => SORT_ASC],
                        'desc' => ['participante.id' => SORT_DESC],
                    ],
                    'estudante' => [
                        'asc' => ['participante.estudante' => SORT_ASC],
                        'desc' => ['participante.estudante' => SORT_DESC],
                    ],
                    'estudante_medicina' => [
                        'asc' => ['participante.estudante_medicina' => SORT_ASC],
                        'desc' => ['participante.estudante_medicina' => SORT_DESC],
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
            'user.id' => $this->id,
            'user.status' => $this->status,
            'user.role' => $this->role,
        ]);

        if ($this->has_participante !== null && $this->has_participante !== '') {
            if ((int) $this->has_participante === 1) {
                $query->andWhere(['is not', 'participante.id', null]);
            } else {
                $query->andWhere(['participante.id' => null]);
            }
        }

        if ($this->estudante !== null && $this->estudante !== '') {
            $query->andWhere(['participante.estudante' => $this->estudante]);
        }

        if ($this->estudante_medicina !== null && $this->estudante_medicina !== '') {
            $query->andWhere(['participante.estudante_medicina' => $this->estudante_medicina]);
        }

        $query->andFilterWhere(['like', 'user.nome', $this->nome])
            ->andFilterWhere(['like', 'user.email', $this->email])
            ->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['like', 'user.cpf', $this->cpf])
            ->andFilterWhere(['like', 'user.created_at', $this->created_at])
            ->andFilterWhere(['like', 'user.updated_at', $this->updated_at]);

        return $dataProvider;
    }
}
