<?php

namespace Core\Modules\Users;

use Core\Models\User;

class UserService implements IUser
{
    public function getUsers()
    {
        return User::all()->toArray();
    }

    public function createUser($data)
    {
        // Lógica para crear un nuevo usuario
    }
}
