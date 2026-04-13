<?php

namespace app\modules\common\models;

use app\modules\common\models\Universidade;
use yii\base\Model;

class ParticipantUniversityCorrectionForm extends Model
{
    public ?int $userId = null;
    public ?int $participacaoId = null;
    public ?int $currentUniversidadeId = null;
    public $universidade_id;

    public function rules(): array
    {
        return [
            [['universidade_id'], 'required'],
            [['universidade_id'], 'integer'],
            [['universidade_id'], 'validateUniversidadeAtiva'],
            [['universidade_id'], 'validateDifferentUniversity'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'universidade_id' => 'Nova universidade',
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->universidade_id = ($this->universidade_id === '' || $this->universidade_id === null)
            ? null
            : (int) $this->universidade_id;

        return true;
    }

    public function validateUniversidadeAtiva($attribute): void
    {
        if ($this->hasErrors($attribute) || $this->$attribute === null) {
            return;
        }

        if (!Universidade::find()->where(['id' => (int) $this->$attribute, 'ativo' => 1])->exists()) {
            $this->addError($attribute, 'Selecione uma universidade ativa e valida.');
        }
    }

    public function validateDifferentUniversity($attribute): void
    {
        if ($this->hasErrors($attribute) || $this->$attribute === null || $this->currentUniversidadeId === null) {
            return;
        }

        if ((int) $this->$attribute === (int) $this->currentUniversidadeId) {
            $this->addError($attribute, 'Selecione uma universidade diferente da atual.');
        }
    }
}
