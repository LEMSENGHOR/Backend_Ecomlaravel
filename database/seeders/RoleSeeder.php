<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'ADMIN'],
            ['name' => 'ADMIN']
        );

        Role::updateOrCreate(
            ['name' => 'USER'],
            ['name' => 'USER']
        );
    }
}