<?php

use App\DataInjection\Injections\Injection;
use App\Models\Role;
use App\Models\User;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $standardRole = Role::query()->where('name', Role::STANDARD_USER_ROLE)->first();

        User::query()->create([
            'first_name' => 'Cron',
            'last_name' => 'User',
            'email' => User::EMAIL_FOR_CRON_USER,
            'status' => User::STATUS_INACTIVE,
            'role_id' => $standardRole->getKey(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        User::query()->where('email', User::EMAIL_FOR_CRON_USER)->delete();
    }
};
