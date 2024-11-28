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
        return $this->success($user['data'], $user['message'], $user['status']);
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
        return $this->success($user['data'], $user['message'], $user['status']);
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
        return $this->success($user['data'], $user['message'], $user['status']);
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
        return $this->success($user['data'], $user['message'], $user['status']);
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
        return $this->success($user['message'], $user['status']);
    }
}
