<?php

namespace App\Services;

use Exception;
use App\Models\User;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\Log;


use function PHPUnit\Framework\isEmpty;

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

            $user = User::findOrFail($user->id);

            Cache::forget("user_{$user->id}");
            Cache::forget('users_list');

            $user = User::findOrFail($user->id);
           
            $user = $user->delete();
            return true;
        } catch (\Throwable $e) {
            Log::error('Error deleting User: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to delete user.'], 500));
        }
    }

    //.................................Soft Delete...........................................
    /**
     * force Delete the user if he exsist in the trashed array
     * @param mixed $id
     * @throws \Exception
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return bool
     */
    public function forceDeleteUser($id)
    {
        try {
           
                $arry_of_deleted_users = User::onlyTrashed()->pluck('id')->toArray();
               
                if(in_array($id,$arry_of_deleted_users))
                {
                    $user = User::onlyTrashed()->find($id);
                    $user->forceDelete();
                    return true;
                }else{
                    throw new Exception("This id is not Deleted yet,or dosn't exist!!");
                }
    
        } catch (Exception $e) {
            Log::error('Error while  Force Deliting  the User' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed in the server : '.$e->getMessage()], 500));
        }      
        
    }

    //.......................................

    /**
     * restore a deleted user 
     * @param mixed $id
     * @throws \Exception
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function restoreUser($id)
    {
        try {

            //find out if the given id exsist as deleted element
            $user = User::onlyTrashed()->find($id);

            if(is_null($user))
            {
                throw new Exception("This id is not Deleted yet,or dosn't exist!!");
            }
            $user->restore();
            return true;

        } catch (Exception $e) {
            Log::error('Error while  Restoring the user ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed in the server : '.$e->getMessage()], 500));
        }

    }

    //..........................................

    /**
     * get All Trashed users
     * @throws \Exception
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed
     */
    public function getAllTrashedUsers()
    {
       try {
           $users = User::onlyTrashed()->get();
           if($users->isEmpty())
           {
               throw new Exception('There are no Deleted useres');
           }
           return $users;
       } catch (Exception $e) {
           Log::error('Error while  get all trashed users ' . $e->getMessage());
           throw new HttpResponseException(response()->json(['message' => 'Failed in the server : '.$e->getMessage()], 500));
       }
    }
}