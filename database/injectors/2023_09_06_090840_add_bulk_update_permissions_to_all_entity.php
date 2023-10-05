<?php

use App\DataInjection\Injections\Injection;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $existedCustomFieldsWithBulkUpdatePermissions = Permission::query()
            ->where('action', Permission::ACTION_BULK_UPDATE)->get()->keyBy('custom_field_id')->toArray();

        Permission::query()
            ->where('action', Permission::ACTION_UPDATE)->whereNotIn(
                'custom_field_id',
                array_keys($existedCustomFieldsWithBulkUpdatePermissions),
            )->chunkById(
                50,
                function (Collection $items) {
                    /** @var Permission $item */
                    foreach ($items as $item) {
                        Permission::query()->insert([
                            'custom_field_id' => $item->custom_field_id,
                            'action' => Permission::ACTION_BULK_UPDATE,
                            'created_at' => now(),
                        ]);
                    }
                },
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
