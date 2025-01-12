<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserService
{
    /**
     * Get a paginated list of users with caching.
     *
     * @return array
     * @throws HttpResponseException
     */
    public function listUser()
    {
        try {
           
            $users = cacheData('users_list', function () {
                return User::paginate()->toArray();
            });

            return $users;
        } catch (\Throwable $e) {
            Log::error('Error getting all Users: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve users.'], 500));
        }
    }

    /**
     * Create a new user with valid data and clear cache.
     *
     * @param array $data
     * @return User
     * @throws HttpResponseException
     */
    public function createUser(array $data)
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            Cache::forget('users_list');
            return $user;
        } catch (\Throwable $e) {
            Log::error('Error creating User: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to create user.'], 500));
        }
    }

    /**
     * Get user data with caching.
     *
     * @param User $user
     * @return User
     * @throws HttpResponseException
     */
    public function getUser(User $user)
    {
        try {
            return cacheData("user_{$user->id}", function () use ($user) {
                return $user;
            });
        } catch (\Throwable $e) {
            Log::error('Error getting user: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve user.'], 500));
        }
    }

    /**
     * Update user data and clear cache.
     *
     * @param User $user
     * @param array $data
     * @return User
     * @throws HttpResponseException
     */
    public function updateUser(User $user, array $data)
    {
        try {
            $user->update(array_filter($data));

            Cache::forget("user_{$user->id}");
            Cache::forget('users_list');
            return $user;
        } catch (\Throwable $e) {
            Log::error('Error updating User: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to update user.'], 500));
        }
    }

    /**
     * Delete a user and clear cache.
     *
     * @param User $user
     * @return bool
     * @throws HttpResponseException
     */
    public function deleteUser(User $user)
    {
        try {
            $user->delete();

            Cache::forget("user_{$user->id}");
            Cache::forget('users_list');
            return true;
        } catch (\Throwable $e) {
            Log::error('Error deleting User: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to delete user.'], 500));
        }
    }
}
