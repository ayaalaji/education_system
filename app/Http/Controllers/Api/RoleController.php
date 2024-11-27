<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateRequest;

class RoleController extends Controller
{
    protected $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->middleware('auth:api');
        $this->middleware('manager');
        $this->middleware('security');
        $this->roleService = $roleService;
    }
    /**
     * display all Roles in our website
     * only manager can get all roles
     * @return /Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $roles = Role::all();
            return $this->success($roles);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->error('Unable to retrieve roles at this time. Please try again later.' , 500);
        }
    }

    /**
     * Store a newly Role by manager.
     * @param StoreRequest $request
     * @return /Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        try {
            $roleData = $request->validated();
            $role = $this->roleService->createRole($roleData);
            return $this->success($role,'Role created successfully',201);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->error($th->getMessage(), 500); 
        }
    }

    /**
     * Display the specified Role by manager.
     * @param string $id
     * @return /Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $role = $this->roleService->showRole($id);
            return $this->success($role);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->error('Unable to retrieve role details at this time. Please try again later.', 500);
        }
    }

    /**
     * Update the specified Role by manager.
     * @param UpdateRequest $request
     * @param string $id
     * @return /Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, string $id)
    {
        try {
            $roleData = $request->validated();
            $role =$this->roleService->updateRole($roleData, $id);
            return $this->success($role,'Role updated successfully');
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->error('Unable to update role at this time. Please try again later.', 500);
        }
    }

    /**
     * Remove the specified Role by manager.
     * @param string $id
     * @return /Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
       try {
            $this->roleService->deleteRole($id);
            return $this->success(null,'Role deleted successfully');
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->error($th->getMessage(), 500);
        }
    }
}
