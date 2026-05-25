<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'         => Role::SUPER_ADMIN,
                'display_name' => 'Super Admin',
                'description'  => 'Full system access including user management and configuration',
                'level'        => Role::LEVEL_SUPER_ADMIN,
            ],
            [
                'name'         => Role::SALES_DIRECTOR,
                'display_name' => 'Sales Director',
                'description'  => 'National visibility, final approval authority',
                'level'        => Role::LEVEL_SALES_DIRECTOR,
            ],
            [
                'name'         => Role::ZONE_MANAGER,
                'display_name' => 'Zone Manager',
                'description'  => 'State/zone level management and approvals',
                'level'        => Role::LEVEL_ZONE_MANAGER,
            ],
            [
                'name'         => Role::BDM,
                'display_name' => 'Business Development Manager',
                'description'  => 'District/city team management and first-level approvals',
                'level'        => Role::LEVEL_BDM,
            ],
            [
                'name'         => Role::BDE,
                'display_name' => 'Business Development Executive',
                'description'  => 'Field sales, lead creation, and quotation drafting',
                'level'        => Role::LEVEL_BDE,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $this->command->info('✅ Roles seeded');
    }
}
