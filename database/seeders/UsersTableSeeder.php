<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class UsersTableSeeder
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $role = Role::query()->where('name', Role::MAIN_ADMINISTRATOR_ROLE)->first();

        foreach ($this->getAdminUsers() as $user) {
            User::query()
                ->create(
                    [
                        'email' => $user['email'],
                        'first_name' =>  $user['first_name'],
                        'last_name' => $user['last_name'],
                        'role_id' => $role->getKey(),
                    ],
                );
        }
    }

    private function getAdminUsers()
    {
        return [
            [
                'first_name' => 'A',
                'last_name' => 'Jay',
                'email' => 'ajay@mvix.com',
            ],
            [
                'first_name' => 'Roman',
                'last_name' => 'Hovtvyan',
                'email' => 'r.hovtvian@gmail.com',
            ],
            [
                'first_name' => 'Oleksii',
                'last_name' => 'Prokhorenko',
                'email' => 'oleksii.p@opsworks.co',
            ],
            [
                'first_name' => 'Alexey',
                'last_name' => 'Chigirev',
                'email' => 'alexey.chigirev12@gmail.com',
            ],
            [
                'first_name' => 'Shitish',
                'last_name' => 'Surani',
                'email' => 'ssurani@mvix.com',
            ],
            [
                'first_name' => 'Sarita',
                'last_name' => 'Shah',
                'email' => 'saritaniravshah@gmail.com',
            ],
        ];
    }
}
