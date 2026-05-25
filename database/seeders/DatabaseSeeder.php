<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TerritorySeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
