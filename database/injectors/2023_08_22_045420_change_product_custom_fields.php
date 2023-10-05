<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\CustomFieldOption;
use App\Models\CustomFieldValues;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $parent = CustomField::query()->where('entity_type', Product::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        foreach ($this->customFieldsToCreate($parent->getKey()) as $customField) {
            CustomField::query()->updateOrCreate(
                ['entity_type' => $customField['entity_type'], 'code' => $customField['code']],
                $customField,
            );
        }
        /** @var CustomField $unitOfMeasure */
        $unitOfMeasure = CustomField::query()->where('entity_type', Product::class)
            ->where('code', 'unit-of-measure')->first();

        $unitOfMeasure->type = CustomField::FIELD_TYPE_SELECT;
        $unitOfMeasure->save();

        $selectOptions = [
            'qty',
            'pieces',
            'unit',
            'each',
            'Each1',
            'EA',
            'per month',
            'per device, per month',
            'per device',
            'per year',
            'per month per screen',
            'per hour',
            'per account, per month',
            'per GB',
            'per device per month',
            'per slot',
            'per month per account',
            'per player per month',
            '1 year',
            'per device, per year',
            'Custom',
        ];


        CustomFieldOption::query()->where('custom_field_id', $unitOfMeasure->getKey())->delete();
        $sortOrder = 1;
        foreach ($selectOptions as $option) {
            CustomFieldOption::query()->create([
                'name' => $option,
                'sort_order' => $sortOrder,
                'custom_field_id' => $unitOfMeasure->getKey(),
            ]);
            $sortOrder++;
        }

        $allAvailableCustomFieldOptions = CustomFieldOption::query()
            ->where('custom_field_id', $unitOfMeasure->getKey())
            ->get()->mapWithKeys(function ($item) {
                return [$item->name => $item->id];
            })->toArray();

        CustomFieldValues::query()->where('field_id', $unitOfMeasure->getKey())->whereNotNull('text_value')->chunkById(
            50,
            function (Collection $items) use (
                $allAvailableCustomFieldOptions,
                $sortOrder,
                $unitOfMeasure
            ) {
                /** @var CustomFieldValues $item */
                foreach ($items as $item) {
                    if (!isset($allAvailableCustomFieldOptions[$item->getAttributes()['text_value']])) {
                        $allAvailableCustomFieldOptions[$item->getAttributes()['text_value']] =
                            CustomFieldOption::query()
                                ->create([
                                    'name' => $item->getAttributes()['text_value'],
                                    'sort_order' => $sortOrder,
                                    'custom_field_id' => $unitOfMeasure->getKey(),
                                ])->getKey();

                        $sortOrder++;
                    }

                    $item->integer_value = $allAvailableCustomFieldOptions[$item->getAttributes()['text_value']];
                    $item->text_value = null;
                    $item->save();
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

    private function customFieldsToCreate($parentId): array
    {
        return [
            [
                'entity_type' => Product::class,
                'code' => 'product-recurring',
                'name' => 'Product recurring',
                'type' => CustomField::FIELD_TYPE_BOOL,
                'sort_order' => 1,
                'parent_id' => $parentId,
            ],
            [
                'entity_type' => Product::class,
                'code' => 'product-price',
                'name' => 'Product price',
                'type' => CustomField::FIELD_TYPE_PRICE,
                'sort_order' => 2,
                'parent_id' => $parentId,
            ],
        ];
    }
};
