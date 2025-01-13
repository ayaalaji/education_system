<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{ protected static ?string $password;
    public function run(): void
    { 
        Teacher::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'specialization'=>'security'
        ])->assignRole('admin');

        Teacher::create([
            'name' => 'teacher',
            'email' => 'teacher@gmail.com',
            'password' => 'password',
            'specialization'=>'mathes'
        ])->assignRole('teacher');

        Teacher::create([
            'name' => 'manager',
            'email' => 'manager@gmail.com',
            'password' => 'password',
            'specialization'=>'db mangment'
        ])->assignRole('manager');
    }
}
