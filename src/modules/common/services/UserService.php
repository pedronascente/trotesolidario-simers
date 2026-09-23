<?php

namespace app\modules\common\services;

use app\models\User;
use app\modules\common\models\ParticipantProfileForm;
use app\modules\common\models\Participacao;
use app\modules\common\models\Participante;
use app\modules\common\models\UserCreateForm;
use app\modules\common\services\contracts\UserServiceInterface;
use Yii;
use yii\db\Expression;
use yii\web\NotFoundHttpException;

class UserService implements UserServiceInterface
{
    public function findById($id): ?User
    {
        return User::findOne($id);
    }

    public function findByCpf($cpf): ?User
    {
        return User::findByCpf($cpf);
    }

    public function findByEmail($email): ?User
    {
        return User::findByEmail($email);
    }

    public function create(UserCreateForm $form): User
    {
        $user = new User();
        $user->scenario = 'create';
        $this->fillUser($user, $form);

        if (!$user->save()) {
            throw new \RuntimeException('Erro ao criar usuario: ' . json_encode($user->errors));
        }

        return $user;
    }

    public function update($id, UserCreateForm $form): User
    {
        $user = $this->findById($id);

        if ($user === null) {
            throw new NotFoundHttpException('Usuario nao encontrado.');
        }

        $this->fillUser($user, $form, false);

        if (!$user->save()) {
            throw new \RuntimeException('Erro ao atualizar usuario: ' . json_encode($user->errors));
        }

        return $user;
    }

    public function delete($id): bool
    {
        $user = $this->findById($id);

        if ($user === null) {
            throw new NotFoundHttpException('Usuario nao encontrado.');
        }

        $user->status = User::STATUS_INACTIVE;

        if (!$user->save(false, ['status', 'updated_at'])) {
            throw new \RuntimeException('Erro ao inativar usuario.');
        }

        return true;
    }

    public function changePassword($id, $newPassword): bool
    {
        $user = $this->findById($id);

        if ($user === null) {
            throw new NotFoundHttpException('Usuario nao encontrado.');
        }

        $user->setPassword($newPassword);
        $user->generateAuthKey();

        if (!$user->save(false, ['password_hash', 'authKey', 'updated_at'])) {
            throw new \RuntimeException('Erro ao alterar senha.');
        }

        return true;
    }

    public function getAll($filters = [], $page = 1, $pageSize = 20): array
    {
        $query = User::find();

        if (!empty($filters['nome'])) {
            $query->andWhere(['like', 'nome', $filters['nome']]);
        }

        if (!empty($filters['email'])) {
            $query->andWhere(['like', 'email', $filters['email']]);
        }

        if (!empty($filters['cpf'])) {
            $query->andWhere(['like', 'cpf', preg_replace('/\D/', '', (string) $filters['cpf'])]);
        }

        if (!empty($filters['username'])) {
            $query->andWhere(['like', 'username', $filters['username']]);
        }

        if (!empty($filters['role'])) {
            $query->andWhere(['role' => $filters['role']]);
        }

        if ($filters['status'] ?? null) {
            $query->andWhere(['status' => $filters['status']]);
        }

        $total = (int) $query->count();
        $data = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->all();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    public function validateLogin($cpf, $password): ?User
    {
        $user = $this->findByCpf($cpf);

        if ($user && $user->validatePassword($password)) {
            return $user;
        }

        return null;
    }

    public function getParticipantProfileData(int $userId): array
    {
        $user = $this->requireUser($userId);
        $participante = $this->findOrCreateParticipante($user);

        return [
            'model' => $this->buildParticipantProfileForm($user, $participante),
            'user' => $user,
            'participante' => $participante,
            'universidadeAtual' => $this->findCurrentUniversityName($user),
        ];
    }

    public function updateParticipantProfile(int $userId, ParticipantProfileForm $form): void
    {
        $user = $this->requireUser($userId);
        $participante = $this->findOrCreateParticipante($user);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $user->nome = $form->nome;
            $user->email = $form->email;
            $user->username = $form->username;
            $user->cpf = $form->cpf;

            if (!$user->save(false, ['nome', 'email', 'username', 'cpf', 'updated_at'])) {
                throw new \RuntimeException('Erro ao atualizar dados do usuario.');
            }

            if ($form->password !== '') {
                $user->setPassword($form->password);
                $user->generateAuthKey();

                if (!$user->save(false, ['password_hash', 'authKey', 'updated_at'])) {
                    throw new \RuntimeException('Erro ao atualizar senha do usuario.');
                }
            }

            $participante->user_id = $user->id;
            $participante->estudante = (int) $form->estudante;
            $participante->estudante_medicina = (int) $form->estudante_medicina;
            $participante->previsao_formatura = $form->previsao_formatura ?: null;

            if (!$participante->save(false)) {
                throw new \RuntimeException('Erro ao atualizar perfil de participante: ' . json_encode($participante->errors));
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function fillUser(User $user, UserCreateForm $form, $isCreate = true): void
    {
        $user->nome = $form->nome;
        $user->email = $form->email;
        $user->username = $form->username;
        $user->cpf = $form->cpf;
        $user->role = $form->role;
        $user->status = $form->status ?: User::STATUS_ACTIVE;

        if (!empty($form->password)) {
            if ($isCreate) {
                $user->password = $form->password;
            }
            $user->setPassword($form->password);
            if ($isCreate || empty($user->authKey)) {
                $user->generateAuthKey();
            }
        }
    }

    private function requireUser(int $userId): User
    {
        $user = $this->findById($userId);
        if ($user === null) {
            throw new NotFoundHttpException('Usuario nao encontrado.');
        }

        return $user;
    }

    private function findOrCreateParticipante(User $user): Participante
    {
        $participante = Participante::findOne(['user_id' => $user->id]);
        if ($participante !== null) {
            return $participante;
        }

        Yii::warning('Perfil de participante ausente para o usuario ID ' . $user->id . '. Um registro sera inicializado no fluxo de perfil.', __METHOD__);

        return new Participante([
            'user_id' => $user->id,
            'estudante' => 1,
            'estudante_medicina' => 0,
        ]);
    }

    private function findCurrentUniversityName(User $user): ?string
    {
        $participacao = Participacao::find()
            ->with('universidade')
            ->where(['user_id' => $user->id])
            ->orderBy(new Expression("CASE WHEN [[status]] = 'ativo' THEN 0 ELSE 1 END ASC, [[id]] DESC"))
            ->one();

        return $participacao && $participacao->universidade
            ? $participacao->universidade->nome
            : null;
    }

    private function buildParticipantProfileForm(User $user, Participante $participante): ParticipantProfileForm
    {
        $form = new ParticipantProfileForm();
        $form->userId = (int) $user->id;
        $form->nome = $user->nome;
        $form->email = $user->email;
        $form->username = $user->username;
        $form->cpf = $user->getCpfFormatado() ?? $user->cpf;
        $form->estudante = (int) $participante->estudante;
        $form->estudante_medicina = (int) $participante->estudante_medicina;
        $form->previsao_formatura = $participante->previsao_formatura;

        return $form;
    }
}
