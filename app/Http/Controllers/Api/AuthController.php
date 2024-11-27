<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
        $this->middleware('security');
        $this->authService = $authService;
    }
    /**
     * register method
     * @param RegisterRequest $request
     * @return /Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request){
        $validatedData =$request->validated();

        $result = $this->authService->register($validatedData);

        return $this->success([
            'user' =>$result['user'],
            'authorisation' => [
                'token' => $result['token'],
                'type' => 'bearer',
            ]
        ], 'User created successfully');
    }

    /**
     * login method
     * @param LoginRequest $request
     * @return /Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $request->validated();
        $credentials = $request->only('email', 'password');

        $result = $this->authService->login($credentials);
        if (!$result) {
            return $this->error('Invalid login');
        }

        return $this->success([
            'user' => $result['user'],
            'authorisation' => [
                'token' => $result['token'],
                'type' => 'bearer',
            ]
        ]);
    }
    /**
     * logout method
     * @return /Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authService->logout();
        return $this->success('Successfully logged out');
    }

    /**
     * refresh token method
     * @return /Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
         $result = $this->authService->refresh();

        return $this->success([
            'user' => $result['user'],
            'authorisation' => [
                'token' => $result['token'],
                'type' => 'bearer',
            ]
        ]);
    }
}
