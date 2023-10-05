<?php

use App\DataInjection\Injections\Injection;
use App\Models\CustomField;
use App\Models\Proposal;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /** @var CustomField $parent */
        $parent = CustomField::query()->where('entity_type', Proposal::class)
            ->where('type', CustomField::FIELD_TYPE_CONTAINER)->first();

        foreach ($this->entityForCreate($parent) as $entity) {
            CustomField::query()->updateOrCreate(
                ['entity_type' => $entity['entity_type'], 'code' => $entity['code']],
                $entity,
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
        //

    }

    private function entityForCreate(CustomField $parent): array
    {
        return
            [
                [
                    'entity_type' => Proposal::class,
                    'code' => 'status',
                    'name' => 'Status',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 1,
                    'parent_id' => $parent->getKey(),
                ],
                [
                    'entity_type' => Proposal::class,
                    'code' => 'template_id',
                    'name' => 'Template id',
                    'type' => CustomField::FIELD_TYPE_INTERNAL,
                    'sort_order' => 2,
                    'parent_id' => $parent->getKey(),
                ],
            ];
    }
};
