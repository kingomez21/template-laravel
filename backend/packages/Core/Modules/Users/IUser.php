<?php

namespace Core\Modules\Users;

interface IUser
{
    public function getUsers();
    public function createUser($data);
}
