<?php

namespace Core\Modules\Users;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    protected $userService;

    public function __construct(IUser $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getUsers();
        // Lógica para mostrar los usuarios

        return response()->json($users);
    }

    public function store($data)
    {
        $this->userService->createUser($data);
        // Lógica para manejar la creación de un nuevo usuario

        return response()->json(['message' => 'Usuario creado exitosamente']);
    }
}
