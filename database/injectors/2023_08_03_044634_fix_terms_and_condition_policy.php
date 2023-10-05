<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\TermsAndConditions;
use App\Policies\TermsAndConditionPolicy;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        CustomField::query()->where('entity_type', TermsAndConditionPolicy::class)
            ->update(['entity_type' => TermsAndConditions::class]);
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
