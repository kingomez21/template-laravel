<?php

namespace Core\Modules\Users;


class UserService implements IUser
{
    public function getUsers()
    {
        return [
            ['id' => 1, 'name' => 'Juan Pérez', 'email' => 'juan.perez@example.com'],
            ['id' => 2, 'name' => 'María Gómez', 'email' => 'maria.gomez@example.com'],
        ];
    }

    public function createUser($data)
    {
        // Lógica para crear un nuevo usuario
    }
}
