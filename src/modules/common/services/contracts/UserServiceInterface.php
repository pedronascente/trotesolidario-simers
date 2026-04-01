<?php

namespace app\modules\common\services\contracts;

use app\models\User;
use app\modules\common\models\UserCreateForm;

interface UserServiceInterface
{
    public function findById($id): ?User;
    public function findByCpf($cpf): ?User;
    public function findByEmail($email): ?User;
    public function create(UserCreateForm $form): User;
    public function update($id, UserCreateForm $form): User;
    public function delete($id): bool;
    public function changePassword($id, $newPassword): bool;
    public function getAll($filters = [], $page = 1, $pageSize = 20): array;
    public function validateLogin($cpf, $password): ?User;
}
