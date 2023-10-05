<?php

use App\DataInjection\Injections\Injection;
use App\Models\Account;
use App\Models\CustomField;
use App\Models\CustomFieldValues;
use Illuminate\Database\Eloquent\Collection;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        /** @var CustomField $salesTaxCustomField */
        $salesTaxCustomField = CustomField::query()->where('entity_type', Account::class)
            ->where('code', 'sales-tax')->whereNull('deleted_at')->first();

        if ($salesTaxCustomField) {
            $salesTaxCustomField->type = CustomField::FIELD_TYPE_BOOL;
            $salesTaxCustomField->save();


            $textValuesExtempt = [
                'true',
                Account::EXEMPT_TAX_STATUS,
            ];

            CustomFieldValues::query()->where('field_id', $salesTaxCustomField->getKey())->chunkById(
                50,
                function (Collection $items) use ($textValuesExtempt) {
                    /** @var CustomFieldValues $item */
                    foreach ($items as $item) {
                        if (in_array($item->getAttributes()['text_value'], $textValuesExtempt)) {
                            $item->boolean_value = 0;
                        } else {
                            $item->boolean_value = 1;
                        }

                        $item->save();
                    }
                },
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
        /** @var CustomField $salesTaxCustomField */
        $salesTaxCustomField = CustomField::query()->where('entity_type', Account::class)
            ->where('code', 'sales-tax')->whereNull('deleted_at')->first();

        if ($salesTaxCustomField) {
            $salesTaxCustomField->type = CustomField::FIELD_TYPE_TEXT;
            $salesTaxCustomField->save();


            CustomFieldValues::query()->where('field_id', $salesTaxCustomField->getKey())->chunkById(
                50,
                function (Collection $items) {
                    /** @var CustomFieldValues $item */
                    foreach ($items as $item) {
                        if ($item->boolean_value == 1) {
                            $item->text_value = 'false';
                        } else {
                            $item->boolean_value = Account::EXEMPT_TAX_STATUS;
                        }

                        $item->save();
                    }
                },
            );
        }
    }
};
