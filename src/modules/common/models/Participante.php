<?php

namespace app\modules\common\models;

use app\models\User;
use yii\db\ActiveRecord;

class Participante extends ActiveRecord
{
    public static function tableName()
    {
        return 'participante';
    }

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'estudante', 'estudante_medicina'], 'integer'],
            [['previsao_formatura'], 'safe'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['previsao_formatura'], 'required', 'when' => function (self $model) {
                return (int) $model->estudante === 1;
            }, 'whenClient' => "function () { return $('#participante-estudante').is(':checked') || $('#participante-estudante').val() === '1'; }"],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Usuario',
            'estudante' => 'Estudante',
            'estudante_medicina' => 'Estudante de medicina',
            'previsao_formatura' => 'Previsao de formatura',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->previsao_formatura === '') {
            $this->previsao_formatura = null;
        } elseif (!empty($this->previsao_formatura) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $this->previsao_formatura)) {
            $this->previsao_formatura = str_replace('T', ' ', $this->previsao_formatura) . ':00';
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();

        if (!empty($this->previsao_formatura)) {
            $this->previsao_formatura = date('Y-m-d\TH:i', strtotime($this->previsao_formatura));
        }
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->estudante = (int) $this->estudante;
        $this->estudante_medicina = (int) $this->estudante_medicina;

        return true;
    }
}
