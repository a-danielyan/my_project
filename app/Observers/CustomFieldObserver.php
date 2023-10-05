<?php

namespace App\Observers;

use App\Models\CustomField;
use App\Models\Permission;
use App\Models\Role;

class CustomFieldObserver
{
    /**
     * Handle the CustomField "created" event.
     */
    public function created(CustomField $customField): void
    {
        Permission::query()->insert([
            [
                'custom_field_id' => $customField->getKey(),
                'action' => Permission::ACTION_CREATE,
                'created_at' => now(),
            ],
            [
                'custom_field_id' => $customField->getKey(),
                'action' => Permission::ACTION_READ,
                'created_at' => now(),
            ],
            [
                'custom_field_id' => $customField->getKey(),
                'action' => Permission::ACTION_UPDATE,
                'created_at' => now(),
            ],
            [
                'custom_field_id' => $customField->getKey(),
                'action' => Permission::ACTION_BULK_UPDATE,
                'created_at' => now(),
            ],
            [
                'custom_field_id' => $customField->getKey(),
                'action' => Permission::ACTION_DELETE,
                'created_at' => now(),
            ],
        ]);

        $customFieldKeys = Permission::query()->where('custom_field_id', $customField->getKey())->get(['id'])
            ->pluck('id')->toArray();

        $adminRole = Role::query()->where('name', Role::MAIN_ADMINISTRATOR_ROLE)->first();
        $adminRole->permissions()->attach($customFieldKeys);
    }
}
