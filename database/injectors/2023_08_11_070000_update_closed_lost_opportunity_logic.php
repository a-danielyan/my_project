<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\CustomFieldOption;
use App\Models\Opportunity;
use App\Models\Stage;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Stage::query()->where('name', 'Closed-Lost to Competition')->forceDelete();

        $parent = CustomField::query()->where('entity_type', Opportunity::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        $closedLostReasonCustomField = CustomField::query()->updateOrCreate(
            ['entity_type' => Opportunity::class, 'code' => 'lost-reason'],
            [
                'entity_type' => Opportunity::class,
                'code' => 'lost-reason',
                'name' => 'Lost reason',
                'type' => CustomField::FIELD_TYPE_SELECT,
                'sort_order' => 1,
                'parent_id' => $parent->getKey(),
            ],
        );

        CustomFieldOption::query()->where('custom_field_id', $closedLostReasonCustomField->getKey())->delete();

        $sortOrder = 1;
        foreach ($this->getAvailableClosedLostReason() as $lostReason) {
            CustomFieldOption::query()->create([
                'name' => $lostReason,
                'sort_order' => $sortOrder,
                'custom_field_id' => $closedLostReasonCustomField->getKey(),
            ]);
            $sortOrder++;
        }
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

    private function getAvailableClosedLostReason(): array
    {
        return [
            'Competition',
            'Pricing',
            'Decided to do nothing',
            'No Budget',
            'No Communication',
            'Software too Complicated',
            'Other',
        ];
    }
};
