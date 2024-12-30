<?php
 
namespace  App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthService {
    /**
     * register method
     * @param array $data
     */
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
          
        ]);

        $token = JWTAuth::fromUser($user);
        return [
            'user' => $user,
            'token' => $token
        ];
    }
    /**
     * Login method for a specific guard
     * @param array $credentials
     * @param string $guard
     * @return array|bool
     */
    public function login(array $credentials, string $guard)
    {
        Auth::shouldUse($guard);
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return false;
        }

        $user = Auth::user();
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Logout method for a specific guard
     * @param string $guard
     * @return void
     */
    public function logout(string $guard)
    {
        Auth::shouldUse($guard);
        Auth::logout();
    }

    /**
     * Refresh token method for a specific guard
     * @param string $guard
     * @return array
     */
    public function refresh(string $guard)
    {
        Auth::shouldUse($guard);
        $token = Auth::refresh();
        $user = Auth::user();
        return [
            'user' => $user,
            'token' => $token
        ];
    }
}