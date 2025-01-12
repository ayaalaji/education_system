<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    /**
     * Constracor to inject user Service
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:teacher-api');
        $this->middleware('security');
        $this->userService = $userService;
    }



    /**
     * Display a listing of the users.
     * Calls the listUser method from UserService to get paginated users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = $this->userService->listUser();
        if (!$user) {
            return $this->error('Getting users failed');
        }
        if (empty($user['data'])) {
            return $this->success(null, 'there is no users yet', 200);
        }
        return $this->success($user, 'Get users list successfully', 200);
    }

    /**
     * Store a newly created user in storage.
     * Calls the createUser method in UserService with validated data from formRequest.
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $user = $this->userService->createUser($validatedData);
        if (!$user) {
            return $this->error('Creating User faild');
        }
        return $this->success($user, 'User Created Successfully', 201);
    }



    /**
     * Display specified user data.
     * Calls the getUser method from UserService with user object to get user data from database.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $user = $this->userService->getUser($user);
        if (!$user) {
            return $this->error('Get user data faild');
        }
        return $this->success($user, 'Get user successfully', 200);
    }


    /**
     * Update specified user with new data in storage.
     * Calls the updateUser method in UserService with user object and validated data from formRequest.
     *
     * @param StoreUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        $user = $this->userService->updateUser($user, $validatedData);
        if (!$user) {
            return $this->error('Update User faild');
        }
        return $this->success($user, 'User Updated Successfully', 201);
    }



    /**
     * Delete specified user with new data from database.
     * Calls the deleteUser method in UserService with user object.
 
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $user = $this->userService->deleteUser($user);
        if (!$user) {
            return $this->error('Delete User faild');
        }
        return $this->success(null, 'User Deleted Successfully', 201);
    }

     //........................................SoftDeletes..............................................

    /**
     * Force Delete the user
     */
    public function forceDeleteUser(string $id)
    {
        $this->userService->forceDeleteUser($id);

        return $this->success(null,'Force Deleted User Successfully',200);
    }

    //...................................................................
    //...................................................................
    /**
     * Rstore a deleted user
     */
    public function restoreUser(string $id)
    {
        $this->userService->restoreUser( $id);

        return $this->success(null,'Restore User Successfully',200); 
    }


    /**
     * get All Trashed users
     */
    public function getAllUserTrashed()
    {
        $users = $this->userService->getAllTrashedUsers();

        return $this->success($users,'Get All Trashed Users Successfully');
     }

}
