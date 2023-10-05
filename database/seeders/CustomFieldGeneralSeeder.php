<?php

namespace Database\Seeders;

use App\Models\AccountPartnershipStatus;
use App\Models\Activity;
use App\Models\ContactAuthority;
use App\Models\ContactType;
use App\Models\CustomField;
use App\Models\Industry;
use App\Models\Invoice;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\LeadType;
use App\Models\License;
use App\Models\Payment;
use App\Models\PaymentProfile;
use App\Models\Template;
use App\Models\Role;
use App\Models\SalesTax;
use App\Models\Solutions;
use App\Models\Stage;
use App\Models\Tag;
use App\Models\TermsAndConditions;
use App\Models\User;
use App\Policies\TermsAndConditionPolicy;
use Illuminate\Database\Seeder;

class CustomFieldGeneralSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // this will be used to manage permissions for routes
        CustomField::query()->create(
            [
                'entity_type' => User::class,
                'code' => 'user-management',
                'name' => 'User management',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        // this will be used for manage access to role management
        CustomField::query()->create(
            [
                'entity_type' => Role::class,
                'code' => 'role-management',
                'name' => 'Role management',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        // this will be used for custom field management
        CustomField::query()->create(
            [
                'entity_type' => CustomField::class,
                'code' => 'custom-field-management',
                'name' => 'Custom field management',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => Industry::class,
                'code' => 'industry',
                'name' => 'Industry',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => Solutions::class,
                'code' => 'solutions',
                'name' => 'Solutions',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => LeadSource::class,
                'code' => 'lead-source',
                'name' => 'Lead Source',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => LeadStatus::class,
                'code' => 'lead-status',
                'name' => 'Lead Status',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => LeadType::class,
                'code' => 'lead-type',
                'name' => 'Lead Type',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => Stage::class,
                'code' => 'stage',
                'name' => 'Stage',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => Activity::class,
                'code' => 'activity',
                'name' => 'Activity',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        CustomField::query()->create(
            [
                'entity_type' => Tag::class,
                'code' => 'tag',
                'name' => 'Tag',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        CustomField::query()->create(
            [
                'entity_type' => License::class,
                'code' => 'license',
                'name' => 'License',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->create(
            [
                'entity_type' => AccountPartnershipStatus::class,
                'code' => 'account-partnership',
                'name' => 'Account partnership',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        CustomField::query()->create(
            [
                'entity_type' => ContactAuthority::class,
                'code' => 'contact-authority',
                'name' => 'Contact authority',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        CustomField::query()->create(
            [
                'entity_type' => ContactType::class,
                'code' => 'contact-type',
                'name' => 'Contact type',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        CustomField::query()->updateOrCreate(
            ['entity_type' => Invoice::class, 'type' => CustomField::FIELD_TYPE_CONTAINER],
            [
                'entity_type' => Invoice::class,
                'code' => 'invoice',
                'name' => 'Invoice',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Template::class, 'type' => CustomField::FIELD_TYPE_CONTAINER],
            [
                'entity_type' => Template::class,
                'code' => 'pdf-template',
                'name' => 'Pdf template',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => Payment::class, 'type' => CustomField::FIELD_TYPE_CONTAINER],
            [
                'entity_type' => Payment::class,
                'code' => 'payments',
                'name' => 'Payments',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => PaymentProfile::class, 'type' => CustomField::FIELD_TYPE_CONTAINER],
            [
                'entity_type' => PaymentProfile::class,
                'code' => 'payment-profile',
                'name' => 'Payment profile',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        CustomField::query()->updateOrCreate(
            ['entity_type' => SalesTax::class, 'type' => CustomField::FIELD_TYPE_CONTAINER],
            [
                'entity_type' => SalesTax::class,
                'code' => 'sales-tax',
                'name' => 'Sales tax',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
        CustomField::query()->updateOrCreate(
            ['entity_type' => TermsAndConditions::class, 'type' => CustomField::FIELD_TYPE_CONTAINER],
            [
                'entity_type' => TermsAndConditionPolicy::class,
                'code' => 'terms-and-condition',
                'name' => 'Terms and condition',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );
    }
}
