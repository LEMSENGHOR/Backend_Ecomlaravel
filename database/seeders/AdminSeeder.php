<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'ADMIN')->firstOrFail();

        $admin = User::updateOrCreate(
            [
                'email' => 'lemsenghor255@gmail.com',
            ],
            [
                'name' => 'Senghor Admin',
                'password' => Hash::make('10052005@#'),
                'status' => 'ACTIVE',
            ]
        );

        $admin->roles()->sync([$adminRole->id]);
    }
}