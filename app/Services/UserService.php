<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isEmpty;

class UserService
{
    /**
     * Get List of users 
     *
     * @return \App\Models\User
     */
    public function listUser()
    {
        try {
            $user = User::paginate();
            $user = $user->toArray();

            return $user;
        } catch (\Throwable $e) {
            Log::error('Error getting all Users: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new user with valid data.
     *
     * @param array $data
     * @return \App\Models\User
     */
    public function createUser(array $data)
    {

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            return $user;
        } catch (\Throwable $e) {
            Log::error('Error creating User: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Get user data
     *
     * @param User $user
     * @return \App\Models\User
     */
    public function getUser(User $user)
    {
        try {
            return $user;
        } catch (\Throwable $e) {
            Log::error('Error getting user: ' . $e->getMessage());
            return false;
        }
    }
    /**
     *update user data.
     *
     * @param User $user
     * @param array $data
     * @return \App\Models\User
     */
    public function updateUser(User $user, array $data)
    {
        try {
            $user->update(array_filter($data));
            return $user;
        } catch (\Throwable $e) {
            Log::error('Error Updating User: ' . $e->getMessage());
            return false;
        }
    }
    /**
     * Delete User
     *
     * @param User $user
     * @return successful message
     */
    public function deleteUser(User $user)
    {
        try {
            $user = User::findOrFail($user);
            $user = $user->delete();
            return true;
        } catch (\Throwable $e) {
            Log::error('Error deleting User: ' . $e->getMessage());
            return false;
        }
    }
}
