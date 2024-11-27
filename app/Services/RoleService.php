<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleService {
    /**
     * create new Role by manager.
     * @param array $data
     */
    public function createRole(array $data)
    {
        try {

            $role = Role::create([
                'name' => $data['name'],
            ]);
            /**
             * If the 'permission' key is not set or the permissions array is empty
             */
            if (!isset($data['permission']) || empty($data['permission'])) {
                //Retrieve all permission IDs from the 'permissions' table and store them in an array
                $defaultPermissions = Permission::pluck('id')->all(); 
                //Sync the role with all permissions (assign all permissions to the role)
                $role->syncPermissions($defaultPermissions);
            }
            //If the 'permission' key is set and contains values
            else {
                // Retrieve the permission IDs from the 'permissions' table that match the provided IDs in $data['permission']
                $permissions = Permission::whereIn('id', $data['permission'])->pluck('id')->all();
                //Sync the role with the specific permissions provided in the input data
                $role->syncPermissions($permissions);
            }



            return $role;
        } catch (\Throwable $th) {
            Log::error('Error creating role: ', ['error' => $th->getMessage()]);
            throw ValidationException::withMessages(['error' => 'Unable to create role at this time. Please try again later.']);
        }
    }
    /**
     * show specifice Role by manager.
     * @param string $id
     */
    public function showRole(string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
                ->where("role_has_permissions.role_id", $id)
                ->get();

            return [
                'role' => $role,
                'rolePermissions' => $rolePermissions
            ];
        } catch (\Throwable $th) {
            Log::error($th);
            throw new \Exception('Unable to retrieve role details at this time. Please try again later.');
        }
    }
    /**
     * update specifice Role by manager.
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
            

            return $role;
        } catch (\Throwable $th) {
            Log::error($th);
            throw ValidationException::withMessages(['error' => 'Unable to update role at this time. Please try again later.']);
        }


    }

    /**
     * delete specifice Role by manager.
     * @param string $id
     * @param string $newRoleName
     */
    public function deleteRole(string $id, $newRoleName = 'Customer')
    {
        try {
            $role = Role::findOrFail($id);
            $roleName = $role->name;
            $this->reassignRoleToUsers($roleName, $newRoleName);
            $role->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error($th);
            throw new \Exception('Unable to delete role at this time. Please try again later.');
        }
    }

    private function reassignRoleToUsers($deletedRoleName, $newRoleName)
    {
        try {
            $users = User::role($deletedRoleName)->get();
            foreach ($users as $user) {
                $user->syncRoles([$newRoleName]);
            }
        } catch (\Throwable $th) {
            Log::error($th);
            throw new \Exception('Unable to reassign roles at this time. Please try again later.');
        }
    }
}