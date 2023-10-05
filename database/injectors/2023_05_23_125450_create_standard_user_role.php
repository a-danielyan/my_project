<?php

use App\DataInjection\Injections\Injection;
use App\Models\Account;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\RoleHasPermission;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $role = Role::query()->firstOrCreate([ 'name' => Role::STANDARD_USER_ROLE],[
            'name' => Role::STANDARD_USER_ROLE,
            'description' => 'Standard user role',
            'created_by' => 1,
        ]);
        $permissions = Permission::query()->with([
            'customField' => function ($query) {
                $query->whereIn('entity_type', [Lead::class, Contact::class, Account::class, Product::class]);
            },
        ])->where('action', Permission::ACTION_READ)->get();

        foreach ($permissions as $permission) {
            RoleHasPermission::query()->create([
                'permission_id' => $permission->id,
                'role_id' => $role->getKey(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        /** @var Role $role */
        $role = Role::query()->where('name', Role::STANDARD_USER_ROLE)->first();

        RoleHasPermission::query()->where('role_id', $role->id)->delete();
        $role->forceDelete();
    }
};
