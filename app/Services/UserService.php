<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            $user = User::findOrFail($user->id);
           
            $user = $user->delete();
            return true;
        } catch (\Throwable $e) {
            Log::error('Error deleting User: ' . $e->getMessage());
            return false;
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