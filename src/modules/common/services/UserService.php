<?php

namespace app\modules\common\services;

use app\models\User;
use app\modules\common\models\UserCreateForm;
use app\modules\common\services\contracts\UserServiceInterface;
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

    private function fillUser(User $user, UserCreateForm $form, $isCreate = true): void
    {
        $user->nome = $form->nome;
        $user->email = $form->email;
        $user->username = $form->username;
        $user->cpf = $form->cpf;
        $user->role = $form->role;
        $user->status = $form->status ?: User::STATUS_ACTIVE;

        if (!empty($form->password)) {
            $user->setPassword($form->password);
            if ($isCreate || empty($user->authKey)) {
                $user->generateAuthKey();
            }
        }
    }
}
