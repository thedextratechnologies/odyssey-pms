<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', Role::SUPER_ADMIN)->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@odysseyelevators.com'],
            [
                'name'                => 'System Administrator',
                'employee_id'         => 'OE-ADMIN-001',
                'role_id'             => $role->id,
                'password'            => bcrypt('Odyssey@Admin2026'),
                'must_change_password' => true,
                'status'              => 'active',
                'date_of_joining'     => now(),
            ]
        );

        $this->command->info('✅ Super Admin created: admin@odysseyelevators.com / Odyssey@Admin2026');
        $this->command->warn('   ⚠️  Change this password immediately after first login!');
    }
}
