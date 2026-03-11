<?php

namespace Core\Modules\Auth;

use App\Http\Controllers\Controller;
use Core\Modules\Auth\Requests\LoginRequest;
use Core\Modules\Auth\Requests\RegisterRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected IAuth $service)
    {
    }

    public function login(LoginRequest $request)
    {
        $result = $this->service->login($request->validated());

        return response()->json($result);
    }

    public function register(RegisterRequest $request)
    {
        //$result = $this->service->register($request->validated());

        //return response()->json($result, 200);
        return response()->json([], 200);
    }

    public function logout(Request $request)
    {
        $this->service->logout($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }
}
