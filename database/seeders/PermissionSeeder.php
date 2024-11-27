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
        // Define system permissions
        $permissions = [
            // Admin permissions
            'add_user',
            'update_user',
            'delete_user',
            'add_role',
            'update_role',
            'delete_role',
            'add_course',
            'update_course',
            'delete_course',
            'add_category',
            'update_category',
            'delete_category',
            'access_materials',

            // Teacher permissions
            'add_course',
            'update_course',
            'delete_course',

            // Student permissions
            'register_course',
            'access_materials',
            'submit_homework',
        ];

        // Create permissions in the database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles
        $roles = [
            'manager',  // Manager
            'teacher', // Teacher 
            'student', // Student 
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Assign permissions to each role
            switch ($roleName) {
                case 'manager':
                    $role->syncPermissions([
                        'add_user',
                        'update_user',
                        'delete_user',
                        'add_role',
                        'update_role',
                        'delete_role',
                        'add_course',
                        'update_course',
                        'delete_course',
                        'add_category',
                        'update_category',
                        'delete_category',
                        'access_materials',
                        
                    ]);
                    break;

                case 'teacher':
                    $role->syncPermissions([
                        'add_course',
                        'update_course',
                        'delete_course',
                    ]);
                    break;

                case 'student':
                    $role->syncPermissions([
                        'register_course',
                        'access_materials',
                        'submit_homework',
                    ]);
                    break;
            }
        }

        // Output success message
        $this->command->info('Permissions and roles seeded successfully!');
    }
}
