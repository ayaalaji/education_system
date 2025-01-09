<?php

namespace App\Http\Controllers\Api;

use Exception;
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
     * Login method for different guards
     * @param LoginRequest $request
     * @param string $guard
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request, string $guard)
    {
        $request->validated();
        $credentials = $request->only('email', 'password');

        $result = $this->authService->login($credentials, $guard);
        if (!$result) {
            return $this->error('Invalid login', 401);
        }

        return $this->success([
            'user' => $result['user'],
            'authorisation' => [
                'token' => $result['token'],
                'type' => 'bearer',
            ]
        ], 'Login successful');
    }

    /**
     * Logout method for different guards
     * @param string $guard
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(string $guard)
    {
        $this->authService->logout($guard);
        return $this->success(null, 'Successfully logged out');
    }

    /**
     * Refresh token method for different guards
     * @param string $guard
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(string $guard)
    {
        $result = $this->authService->refresh($guard);
        return $this->success([
            'user' => $result['user'],
            'authorisation' => [
                'token' => $result['token'],
                'type' => 'bearer',
            ]
        ]);
    }
}
