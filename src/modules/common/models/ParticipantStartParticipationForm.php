<?php

namespace app\modules\common\models;

use app\models\User;
use yii\base\Model;

class ParticipantStartParticipationForm extends Model
{
    public $cpf;

    public function rules(): array
    {
        return [
            [['cpf'], 'required', 'message' => 'Informe o CPF para continuar.'],
            [['cpf'], 'string', 'max' => 14],
            [['cpf'], 'validateCpf'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cpf' => 'CPF',
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->cpf = preg_replace('/\D/', '', (string) $this->cpf);

        return true;
    }

    public function validateCpf($attribute): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if (!$this->isValidCpf((string) $this->$attribute)) {
            $this->addError($attribute, 'CPF invalido. Verifique o numero informado e tente novamente.');
        }
    }

    public function findExistingUser(): ?User
    {
        return $this->lookupUserByCpf((string) $this->cpf);
    }

    public function getFormattedCpf(): string
    {
        $cpf = preg_replace('/\D/', '', (string) $this->cpf);

        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf) ?: $cpf;
    }

    protected function lookupUserByCpf(string $cpf): ?User
    {
        return User::findByCpf($cpf);
    }

    private function isValidCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;

            for ($c = 0; $c < $t; $c++) {
                $sum += (int) $cpf[$c] * (($t + 1) - $c);
            }

            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $cpf[$c] !== $digit) {
                return false;
            }
        }

        return true;
    }
}
