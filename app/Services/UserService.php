<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Get List of users 
     *
     * @return \App\Models\User
     * @throws Exception
     */
    public function listUser()
    {
        try {
            $user = User::paginate();
            if ($user['data'] == []) {
                return [
                    'message' => 'there is no users yet',
                    'data' => null,
                    'status' => 200
                ];
            }

            return [
                'message' => 'Get users list successfully',
                'data' => $user,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error getting all Users: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'User creation failed'], status: 500));
        }
    }

    /**
     * Create a new user with valid data.
     *
     * @param array $data
     * @return \App\Models\User
     * @throws Exception
     */
    public function createUser(array $data)
    {

        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            return [
                'message' => 'User Created Successfully',
                'data' => $user,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error creating User: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'User creation failed'], status: 500));
        }
    }
    /**
     * Get user data
     *
     * @param User $user
     * @return \App\Models\User
     * @throws Exception
     */
    public function getUser(User $user)
    {
        try {
            return [
                'message' => 'Get user successfully',
                'data' => $user,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error getting user: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Get user failed'], status: 500));
        }
    }
    /**
     *update user data.
     *
     * @param User $user
     * @param array $data
     * @return \App\Models\User
     * @throws Exception
     */
    public function updateUser(User $user, array $data)
    {
        try {
            $user->update(array_filter($data));
            return [
                'message' => 'User Updated Successfully',
                'data' => $user,
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error Updating User: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'User updating failed'], status: 500));
        }
    }
    /**
     * Delete User
     *
     * @param User $user
     * @return successful message
     * @throws Exception
     */
    public function deleteUser(User $user)
    {
        try {
            $user = $user->delete();
            return [
                'message' => 'User Updated Successfully',
                'status' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error deleting User: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'User updating failed'], status: 500));
        }
    }
}
