<?php

namespace app\services\auth;

interface AuthServiceInterface
{
    public function login(string $cpf, string $password): bool;
} 