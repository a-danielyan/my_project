<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->getBaseRoles() as $role) {
            Role::query()->create($role);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseRoles(): array
    {
        return [
            [
                'name' => Role::MAIN_ADMINISTRATOR_ROLE,
                'description' => 'System Admin Account',
                'created_by' => 1,
            ],
            [
                'name' => 'Solution consultant',
                'description' => 'Solution consultant',
                'created_by' => 1,
            ],
            [
                'name' => 'Retail Team',
                'description' => 'Retail Team',
                'created_by' => 1,
            ],
            [
                'name' => Role::STANDARD_USER_ROLE,
                'description' => 'Standard user role',
                'created_by' => 1,
            ],
        ];
    }
}
