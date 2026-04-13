<?php

namespace app\modules\common\models;

use app\modules\common\models\Universidade;
use yii\base\Model;

class ParticipantUniversityCorrectionRequestForm extends Model
{
    public ?int $userId = null;
    public ?int $participacaoId = null;
    public ?int $currentUniversidadeId = null;
    public $new_universidade_id;
    public $motivo;

    public function rules(): array
    {
        return [
            [['new_universidade_id', 'motivo'], 'required'],
            [['new_universidade_id'], 'integer'],
            [['motivo'], 'string', 'min' => 10, 'max' => 1000],
            [['new_universidade_id'], 'validateUniversidadeAtiva'],
            [['new_universidade_id'], 'validateDifferentUniversity'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'new_universidade_id' => 'Nova universidade',
            'motivo' => 'Motivo da solicitacao',
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->new_universidade_id = ($this->new_universidade_id === '' || $this->new_universidade_id === null)
            ? null
            : (int) $this->new_universidade_id;
        $this->motivo = trim((string) $this->motivo);

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
