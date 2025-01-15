<?php
namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;

class RoleService
{
    /**
     * Create a new Role by the admin.
     * @param array $data
     */
    public function createRole(array $data)
    {
        try {
            $role = Role::create([
                'name' => $data['name'],
            ]);

            if (!isset($data['permission']) || empty($data['permission'])) {
                $defaultPermissions = Permission::pluck('id')->all();
                $role->syncPermissions($defaultPermissions);
            } else {
                $permissions = Permission::whereIn('id', $data['permission'])->pluck('id')->all();
                $role->syncPermissions($permissions);
            }

            return $role;
        } catch (\Throwable $th) {
            Log::error('Error creating role: ', ['error' => $th->getMessage()]);
            throw ValidationException::withMessages(['error' => 'Unable to create role at this time. Please try again later.']);
        }
    }

    /**
     * Show specific Role by the admin.
     * @param string $id
     */
    public function showRole(string $id)
    {
        try {
            $role = cacheData("role_{$id}", function () use ($id) {
                return Role::findOrFail($id);
            });

            $rolePermissions = cacheData("role_permissions_{$id}", function () use ($id) {
                return Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                    ->where("role_has_permissions.role_id", $id)
                    ->get();
            });

            return [
                'role' => $role,
                'rolePermissions' => $rolePermissions,
            ];
        } catch (\Throwable $th) {
            Log::error($th);
            throw new \Exception('Unable to retrieve role details at this time. Please try again later.');
        }
    }

    /**
     * Update specific Role by the admin.
     * @param array $data
     * @param string $id
     */
    public function updateRole(array $data, string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $updatedRoleData = array_filter([
                'name' => $data['name'] ?? $role->name,
            ]);
            $role->update($updatedRoleData);

            if (isset($data['permission'])) {
                $role->syncPermissions($data['permission']);
            }

            Cache::forget("role_{$id}");
            Cache::forget("role_permissions_{$id}");

            return $role;
        } catch (\Throwable $th) {
            Log::error($th);
            throw ValidationException::withMessages(['error' => 'Unable to update role at this time. Please try again later.']);
        }
    }

    /**
     * Delete specific Role by the admin.
     * @param string $id
     * @param string $newRoleName
     */
    public function deleteRole(string $id, $newRoleName = 'Customer')
    {
        try {
            $role = Role::findOrFail($id);
            $roleName = $role->name;

       
            $role->delete();

            Cache::forget("role_{$id}");
            Cache::forget("role_permissions_{$id}");

            return true;
        } catch (\Throwable $th) {
            Log::error($th);
            throw new \Exception('Unable to delete role at this time. Please try again later.');
        }
    }

}


