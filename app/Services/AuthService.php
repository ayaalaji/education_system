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
            'role' =>'student'
        ]);

        $token = JWTAuth::fromUser($user);
        return [
            'user' => $user,
            'token' => $token
        ];
    }
    /**
     * login method
     * @param array $credentials
     */

    public function login(array $credentials)
    {
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
     * logout method
     */
    public function logout()
    {
        Auth::logout();
    }

    /**
     * refresh token method
     */
    public function refresh()
    {
        $token = Auth::refresh();
        $user = Auth::user();
        return [
            'user' => $user,
            'token' => $token
        ];
    }
}