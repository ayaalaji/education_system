<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions_teacher_api = [
            // General permissions for teacher-api
            'show_user',
            'add_user',
            'update_user',
            'delete_user_temporary',
            'restore_user',
            'get_trashed_user',
            'delete_user',

            'show_teacher',
            'add_teacher',
            'update_teacher',
            'delete_teacher_temporary',
            'restore_teacher',
            'delete_teacher',

            'show_role',
            'add_role',
            'update_role',
            'delete_role',

            'show_course',
            'add_course',
            'update_course',
            'delete_course_temporary',
            'restore_course',
            'get_trashed_corse',
            'delete_course',

            'show_category',
            'add_category',
            'update_category',
            'delete_category_temporary',
            'restore_category',
            'delete_category',
            'getTrashed',

            'access_materials',
            'add_material',
            'update_material',
            'delete_material_temporary',
            'restore_material',
            'delete_material',
            
            'set_course_start_time',
            'set_course_end_time',
            'set_registration_start_time',
            'set_registration_end_time',
            'change_the_status_of_course',
            'add_user_to_course'
        ];

        $permissions_api = [
            // Permissions for API (students)
            'register_course',
            'access_materials',
            'submit_homework',
        ];

        // Create permissions for teacher-api guard
        foreach ($permissions_teacher_api as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'teacher-api']);
        }

        // Create permissions for api guard
        foreach ($permissions_api as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Define roles
        $roles = [
            'admin' => 'teacher-api',
            'manager' => 'teacher-api',
            'teacher' => 'teacher-api',
            'student' => 'api',
        ];

        foreach ($roles as $roleName => $guard) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);

            // Assign permissions to each role
            switch ($roleName) {
                case 'admin':
                    $role->syncPermissions(Permission::where('guard_name', 'teacher-api')->pluck('name')->toArray());
                    break;

                case 'manager':
                    $role->syncPermissions([
                        'set_course_start_time',
                        'set_course_end_time',
                        'set_registration_start_time',
                        'set_registration_end_time',
                        'change_the_status_of_course',
                    ]);
                    break;

                case 'teacher':
                    $role->syncPermissions([
                        'add_course',
                        'update_course',
                        'delete_course_temporary',
                        'restore_course',
                        'delete_course',
                        'add_user_to_course',
                        'get_trashed_corse',

                        'add_material',
                        'update_material',
                        'delete_material_temporary',
                        'restore_material',
                        'delete_material',
                        


                    ]);
                    break;

                case 'student':
                    $role->syncPermissions(Permission::where('guard_name', 'api')->pluck('name')->toArray());
                    break;
            }
        }

        // Output success message
        $this->command->info('Permissions and roles seeded successfully!');
    }
}
