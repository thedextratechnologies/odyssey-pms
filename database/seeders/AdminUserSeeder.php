<?php
namespace Database\Seeders;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder {
    public function run(): void {
        $role = Role::where('name', Role::SUPER_ADMIN)->first();
        // Only create if doesn't exist — never overwrite existing admin
        $exists = User::where('email','admin@odysseyelevators.com')->exists();
        if (!$exists) {
            User::create([
                'name'                 => 'System Administrator',
                'employee_id'          => 'OE-ADMIN-001',
                'email'                => 'admin@odysseyelevators.com',
                'role_id'              => $role->id,
                'password'             => bcrypt('Odyssey@Admin2026'),
                'must_change_password' => true,
                'status'               => 'active',
                'date_of_joining'      => now(),
            ]);
            $this->command->info('✅ Super Admin created');
        } else {
            $this->command->info('✅ Super Admin already exists — skipped');
        }
    }
}
