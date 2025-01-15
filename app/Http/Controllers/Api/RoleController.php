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
        $this->middleware('security');
        $this->roleService = $roleService;
    }
    /**
     * display all Roles in our website
     * only admin can get all roles
     * @return /Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return $this->success($roles);
    }

    /**
     * Store a newly Role by admin.
     * @param StoreRequest $request
     * @return /Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $roleData = $request->validated();
        $role = $this->roleService->createRole($roleData);
        return $this->success($role,'Role created successfully',201);
    }

    /**
     * Display the specified Role by admin.
     * @param string $id
     * @return /Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $role = $this->roleService->showRole($id);
        return $this->success($role);
    }

    /**
     * Update the specified Role by admin.
     * @param UpdateRequest $request
     * @param string $id
     * @return /Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, string $id)
    {
        $roleData = $request->validated();
        $role =$this->roleService->updateRole($roleData, $id);
        return $this->success($role,'Role updated successfully');
    }

    /**
     * Remove the specified Role by admin.
     * @param string $id
     * @return /Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $this->roleService->deleteRole($id);
        return $this->success(null,'Role deleted successfully');
    }
}
