<?php

namespace app\modules\common\models;

use app\models\User;
use Yii;
use yii\base\Model;

class ParticipantRegistrationForm extends Model
{
    public $name;
    public $cpf;
    public $password;
    public $email;
    public $estudante;
    public $estudanteMedicina;
    public $estudanteOutros;
    public $previsaoFormatura;
    public $politicaPrivacidade;
    public $politicaImagem;

    public function rules(): array
    {
        return [
            [['name', 'cpf', 'password', 'email', 'estudante'], 'required'],
            [['politicaPrivacidade', 'politicaImagem'], 'required', 'requiredValue' => 1, 'message' => 'Voce precisa aceitar este termo.'],
            [['email'], 'email'],
            [['password'], 'string', 'min' => 6],
            [['name', 'email', 'previsaoFormatura', 'estudanteOutros'], 'string', 'max' => 255],
            [['cpf'], 'string', 'max' => 14],
            [['estudante'], 'in', 'range' => ['Sim', 'Nao']],
            [['estudanteMedicina'], 'in', 'range' => ['Sim', 'Nao']],
            [['cpf'], 'validateCpf'],
            [['email'], 'validateUniqueEmail'],
            [['cpf'], 'validateUniqueCpf'],
            [
                ['previsaoFormatura', 'estudanteMedicina'],
                'required',
                'when' => fn(self $model) => $model->isStudent(),
                'whenClient' => "function () {
                    return document.getElementById('participantregistrationform-estudante').value === 'Sim';
                }",
            ],
            [
                ['estudanteOutros'],
                'required',
                'when' => fn(self $model) => $model->isStudent() && !$model->isMedicineStudent(),
                'whenClient' => "function () {
                    var estudante = document.getElementById('participantregistrationform-estudante');
                    var estudanteMedicina = document.getElementById('participantregistrationform-estudantemedicina');

                    return estudante && estudante.value === 'Sim'
                        && estudanteMedicina && estudanteMedicina.value === 'Nao';
                }",
                'message' => 'Informe o curso.',
            ],
            [['previsaoFormatura'], 'validatePrevisaoFormatura'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Nome completo',
            'cpf' => 'CPF',
            'password' => 'Senha',
            'email' => 'E-mail',
            'estudante' => 'Voce e estudante?',
            'estudanteMedicina' => 'Voce e estudante de medicina?',
            'estudanteOutros' => 'Curso',
            'previsaoFormatura' => 'Previsao de formatura',
            'politicaPrivacidade' => 'Estou de acordo com a politica de privacidade.',
            'politicaImagem' => 'Autorizo o uso de imagem, video e voz.',
        ];
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->name = trim((string) $this->name);
        $this->email = trim((string) $this->email);
        $this->password = trim((string) $this->password);
        $this->cpf = preg_replace('/\D/', '', (string) $this->cpf);
        $this->estudante = $this->normalizeChoice($this->estudante);
        $this->estudanteMedicina = $this->normalizeChoice($this->estudanteMedicina);
        $this->politicaPrivacidade = $this->normalizeCheckbox($this->politicaPrivacidade);
        $this->politicaImagem = $this->normalizeCheckbox($this->politicaImagem);
        $this->estudanteOutros = trim((string) $this->estudanteOutros);
        $this->previsaoFormatura = trim((string) $this->previsaoFormatura);

        if (!$this->isStudent()) {
            $this->estudanteMedicina = 'Nao';
            $this->estudanteOutros = '';
            $this->previsaoFormatura = '';
        }

        return true;
    }

    public function register(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $user = $this->createUserModel();
            $user->scenario = 'create';
            $user->nome = $this->name;
            $user->email = $this->email;
            $user->username = $this->generateUsername();
            $user->cpf = $this->cpf;
            $user->role = User::ROLE_PARTICIPANTE;
            $user->status = User::STATUS_ACTIVE;
            $user->password = $this->password;
            $user->setPassword($this->password);
            $user->generateAuthKey();

            if (!$this->saveUserModel($user)) {
                $this->copyErrors($user);
                $transaction->rollBack();
                return null;
            }

            $participante = $this->createParticipanteModel();
            $participante->user_id = (int) $user->id;
            $participante->estudante = $this->isStudent() ? 1 : 0;
            $participante->estudante_medicina = $this->isMedicineStudent() ? 1 : 0;
            $participante->previsao_formatura = $this->normalizedPrevisaoFormatura();

            if (!$this->saveParticipanteModel($participante)) {
                $this->copyErrors($participante);
                $transaction->rollBack();
                return null;
            }

            $transaction->commit();
            return $user;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('Falha ao registrar participante: ' . $e->getMessage(), __METHOD__);
            $this->addError('email', 'Nao foi possivel concluir o cadastro agora.');
            return null;
        }
    }

    public function validateCpf($attribute): void
    {
        foreach ($this->cpfValidationErrors((string) $this->$attribute) as $error) {
            $this->addError($attribute, $error);
        }
    }

    public function validateUniqueEmail($attribute): void
    {
        if (!$this->hasErrors($attribute) && $this->emailExists((string) $this->$attribute)) {
            $this->addError($attribute, 'Este e-mail ja esta em uso.');
        }
    }

    public function validateUniqueCpf($attribute): void
    {
        if (!$this->hasErrors($attribute) && $this->cpfExists((string) $this->$attribute)) {
            $this->addError($attribute, 'Este CPF ja esta em uso.');
        }
    }

    public function validatePrevisaoFormatura($attribute): void
    {
        if (!$this->isStudent() || $this->hasErrors($attribute)) {
            return;
        }

        if ($this->normalizedPrevisaoFormatura() === null) {
            $this->addError($attribute, 'Informe a previsao de formatura no formato AAAA/MM.');
        }
    }

    protected function createUserModel(): User
    {
        return new User();
    }

    protected function createParticipanteModel(): Participante
    {
        return new Participante();
    }

    protected function saveUserModel(User $user): bool
    {
        return $user->save();
    }

    protected function saveParticipanteModel(Participante $participante): bool
    {
        return $participante->save();
    }

    protected function emailExists(string $email): bool
    {
        return User::find()->where(['email' => $email])->exists();
    }

    protected function cpfExists(string $cpf): bool
    {
        return User::find()->where(['cpf' => $cpf])->exists();
    }

    protected function usernameExists(string $username): bool
    {
        return User::find()->where(['username' => $username])->exists();
    }

    protected function cpfValidationErrors(string $cpf): array
    {
        $user = $this->createUserModel();
        $user->cpf = $cpf;
        $user->validateCpf('cpf');

        return $user->getErrors('cpf');
    }

    private function generateUsername(): string
    {
        $base = $this->buildUsernameBaseFromName();

        if ($base === '') {
            $base = strtolower((string) preg_replace('/[^a-z0-9]+/i', '.', strstr($this->email, '@', true)));
        }

        $base = trim($base, '.');

        if ($base === '') {
            $base = 'participante';
        }

        return $this->buildUniqueUsername($base);
    }

    private function buildUsernameBaseFromName(): string
    {
        $name = trim((string) $this->name);
        if ($name === '') {
            return '';
        }

        $normalized = $this->normalizeUsernameSegment($name);
        if ($normalized === '') {
            return '';
        }

        $parts = preg_split('/\.+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);
        if ($parts === false || $parts === []) {
            return '';
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        return $parts[0] . '.' . $parts[count($parts) - 1];
    }

    private function normalizeUsernameSegment(string $value): string
    {
        $ascii = function_exists('iconv') ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) : $value;
        $ascii = is_string($ascii) ? $ascii : $value;

        return strtolower((string) preg_replace('/[^a-z0-9]+/i', '.', $ascii));
    }

    private function buildUniqueUsername(string $base): string
    {
        $base = trim($base, '.');
        $base = $base !== '' ? $base : 'participante';

        $candidate = substr($base, 0, 80);
        if (!$this->usernameExists($candidate)) {
            return $candidate;
        }

        $suffix = 1;
        while (true) {
            $suffixText = '.' . $suffix;
            $maxBaseLength = 80 - strlen($suffixText);
            $truncatedBase = substr($base, 0, max(1, $maxBaseLength));
            $candidate = rtrim($truncatedBase, '.') . $suffixText;

            if (!$this->usernameExists($candidate)) {
                return $candidate;
            }

            $suffix++;
        }
    }

    private function normalizedPrevisaoFormatura(): ?string
    {
        if (!$this->isStudent() || $this->previsaoFormatura === '') {
            return null;
        }

        if (preg_match('/^(\d{4})\/(\d{2})$/', $this->previsaoFormatura, $matches)) {
            return sprintf('%s-%s-01 00:00:00', $matches[1], $matches[2]);
        }

        return null;
    }

    private function isStudent(): bool
    {
        return $this->estudante === 'Sim';
    }

    private function isMedicineStudent(): bool
    {
        return $this->estudanteMedicina === 'Sim';
    }

    private function normalizeChoice($value): string
    {
        $value = trim((string) $value);
        if (in_array($value, ['N�o', 'Não', 'No'], true)) {
            return 'Nao';
        }

        return $value;
    }

    private function normalizeCheckbox($value): int
    {
        return in_array($value, [1, '1', true, 'true', 'on'], true) ? 1 : 0;
    }

    private function copyErrors($model): void
    {
        foreach ($model->getFirstErrors() as $attribute => $error) {
            $this->addError($attribute, $error);
        }
    }
}
