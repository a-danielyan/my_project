<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Opportunity;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        CustomField::query()->where('entity_type', Opportunity::class)
            ->where('code', 'opportunity-name')
            ->where('type', CustomField::FIELD_TYPE_TEXT)->delete();

        CustomField::query()->where('entity_type', Opportunity::class)
            ->where('code', 'project_name')
            ->where('type', CustomField::FIELD_TYPE_INTERNAL)
            ->update(['code' => 'opportunity_name', 'name' => 'Opportunity name']);
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
