<?php

namespace app\modules\common\models;

use yii\base\Model;

class UniversityCorrectionReviewForm extends Model
{
    public const DECISION_APPROVE = 'approve';
    public const DECISION_REJECT = 'reject';

    public $decision;
    public $review_notes;

    public function rules(): array
    {
        return [
            [['decision'], 'required'],
            [['decision'], 'in', 'range' => [self::DECISION_APPROVE, self::DECISION_REJECT]],
            [['review_notes'], 'string', 'max' => 1000],
            [['review_notes'], 'required', 'when' => fn(self $model) => $model->decision === self::DECISION_REJECT, 'message' => 'Informe o motivo da rejeicao.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'decision' => 'Decisao',
            'review_notes' => 'Observacoes da analise',
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->review_notes = trim((string) $this->review_notes);

        return true;
    }
}
