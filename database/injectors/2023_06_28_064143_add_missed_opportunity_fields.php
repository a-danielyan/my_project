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
        $this->down();

        $parent = CustomField::query()->where('entity_type', Opportunity::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();


        foreach ($this->customFieldsToCreate($parent->getKey()) as $customField) {
            CustomField::query()->create(
                $customField,
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        foreach ($this->customFieldsToCreate(0) as $customField) {
            CustomField::query()->where('entity_type', Opportunity::class)->where('code', $customField['code'])->delete(
            );
        }
    }

    private function customFieldsToCreate($parentId): array
    {
        return [
            [
                'entity_type' => Opportunity::class,
                'code' => 'opportunity-name',
                'name' => 'Opportunity name',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parentId,
            ],
        ];
    }

};
