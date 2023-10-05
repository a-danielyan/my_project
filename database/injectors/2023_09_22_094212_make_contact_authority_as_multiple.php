<?php

use App\DataInjection\Injections\Injection;
use App\Models\Contact;
use App\Models\CustomField;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        CustomField::query()->where('entity_type', Contact::class)
            ->where('code', 'authority')->update(['is_multiple' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        CustomField::query()->where('entity_type', Contact::class)
            ->where('code', 'authority')->update(['is_multiple' => 0]);
    }
};
