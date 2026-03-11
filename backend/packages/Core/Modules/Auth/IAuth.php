<?php

namespace Core\Modules\Auth;

use Core\Models\User;

interface IAuth
{
    /**
     * Authenticate an existing user and return a Sanctum token.
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array;

    /**
     * Register a new user and return a Sanctum token.
     * @param array $data
     * @return array
     */
    public function register(array $data): array;

    /**
     * Revoke the current user's token.
     * @param User $user
     * @return void
     */
    public function logout(User $user): void;
}
