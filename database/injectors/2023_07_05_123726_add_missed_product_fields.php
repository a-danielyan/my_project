<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Product;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->down();

        CustomField::query()->where('code', 'Recurring-frequency')
            ->update(['code' => 'recurring-frequency']);

        $parent = CustomField::query()->where('entity_type', Product::class)
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
            CustomField::query()->where('entity_type', Product::class)->where('code', $customField['code'])->delete();
        }
    }


    private function customFieldsToCreate($parentId): array
    {
        return [
            [
                'entity_type' => Product::class,
                'code' => 'unit-of-measure',
                'name' => 'Unit of Measure',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parentId,
            ],
        ];
    }
};
