<?php

use App\DataInjection\Injections\Injection;
use App\Models\Account;
use App\Models\Contact;
use App\Models\CustomField;
use App\Models\Lead;

return new class extends Injection {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $parent = CustomField::query()->updateOrCreate(
            ['entity_type' => Lead::class, 'code' => 'enriched-information'],
            [
                'entity_type' => Lead::class,
                'code' => 'enriched-information',
                'name' => 'Enriched Information',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        foreach ($this->additionalPeopleFieldsForCreations($parent->getKey()) as $record) {
            $record = array_merge($record, ['entity_type' => Lead::class]);
            CustomField::query()->updateOrCreate(
                ['entity_type' => $record['entity_type'], 'code' => $record['code']],
                $record,
            );
        }

        $parent = CustomField::query()->updateOrCreate(
            ['entity_type' => Contact::class, 'code' => 'enriched-information'],
            [
                'entity_type' => Contact::class,
                'code' => 'enriched-information',
                'name' => 'Enriched Information',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        foreach ($this->additionalPeopleFieldsForCreations($parent->getKey()) as $record) {
            $record = array_merge($record, ['entity_type' => Contact::class]);
            CustomField::query()->updateOrCreate(
                ['entity_type' => $record['entity_type'], 'code' => $record['code']],
                $record,
            );
        }

        $parent = CustomField::query()->updateOrCreate(
            ['entity_type' => Account::class, 'code' => 'enriched-information'],
            [
                'entity_type' => Account::class,
                'code' => 'enriched-information',
                'name' => 'Enriched Information',
                'type' => CustomField::FIELD_TYPE_CONTAINER,
                'sort_order' => 1,
            ],
        );

        foreach ($this->additionalAccountFieldsForCreations($parent->getKey()) as $record) {
            $record = array_merge($record, ['entity_type' => Account::class]);
            CustomField::query()->updateOrCreate(
                ['entity_type' => $record['entity_type'], 'code' => $record['code']],
                $record,
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


    private function additionalPeopleFieldsForCreations(int $parentId): array
    {
        return [
            [
                'code' => 'apollo_id',
                'name' => 'Apollo Id',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'linkedin_url',
                'name' => 'Linkedin url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 2,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'apollo_title',
                'name' => 'Title',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 3,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'twitter_url',
                'name' => 'Twitter url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'github_url',
                'name' => 'Github url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 5,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'facebook_url',
                'name' => 'Facebook url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 6,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'headline',
                'name' => 'Headline',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 7,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'apollo_contact_id',
                'name' => 'Apollo contact id',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 8,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'apollo_contact',
                'name' => 'Apollo contact',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 9,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'apollo_organization_id',
                'name' => 'Apollo organization id',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 10,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'apollo_organization',
                'name' => 'Apollo organization',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 11,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'seniority',
                'name' => 'Seniority',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 12,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'personal_emails',
                'name' => 'Personal emails',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 13,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'departments',
                'name' => 'Departments',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 14,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'subdepartments',
                'name' => 'SubDepartments',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 15,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'functions',
                'name' => 'Functions',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 15,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
        ];
    }

    private function additionalAccountFieldsForCreations(int $parentId): array
    {
        return [
            [
                'code' => 'apollo_id',
                'name' => 'Apollo Id',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 1,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],

            [
                'code' => 'blog_url',
                'name' => 'Blog url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 2,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'angellist_url',
                'name' => 'Angellist url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 3,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'linkedin_url',
                'name' => 'Linkedin url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 4,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'twitter_url',
                'name' => 'Twitter url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 5,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'facebook_url',
                'name' => 'Facebook url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 6,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'languages',
                'name' => 'Languages',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 7,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'alexa_ranking',
                'name' => 'Alexa ranking',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 8,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'linkedin_uid',
                'name' => 'Linkedin uid',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 9,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'founded_year',
                'name' => 'Founded year',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 10,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'logo_url',
                'name' => 'Logo url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 11,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'crunchbase_url',
                'name' => 'Crunchbase url',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 12,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'primary_domain',
                'name' => 'Primary domain',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 13,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'persona_counts',
                'name' => 'Persona counts',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 14,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'industry',
                'name' => 'Industry',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 15,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'keywords',
                'name' => 'Keywords',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 16,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'estimated_num_employees',
                'name' => 'Estimated num employees',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 17,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'addresses',
                'name' => 'Addresses',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 18,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'suborganizations',
                'name' => 'Suborganization',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 19,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'seo_description',
                'name' => 'Seo description',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 20,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'short_description',
                'name' => 'Short description',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 21,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'annual_revenue',
                'name' => 'Annual revenue',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 22,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'total_funding',
                'name' => 'Total funding',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 23,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'latest_funding_round_date',
                'name' => 'Latest funding round date',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 24,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'latest_funding_stage',
                'name' => 'latest funding stage',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 25,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'funding_events',
                'name' => 'Funding events',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 26,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'technology_names',
                'name' => 'Technology names',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 27,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'current_technologies',
                'name' => 'Current technologies',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 28,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'account_id',
                'name' => 'Apollo account id',
                'type' => CustomField::FIELD_TYPE_TEXT,
                'sort_order' => 29,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'account',
                'name' => 'Apollo account',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 30,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
            [
                'code' => 'departmental_head_count',
                'name' => 'Departmental head count',
                'type' => CustomField::FIELD_TYPE_JSON,
                'sort_order' => 31,
                'parent_id' => $parentId,
                'created_by' => 1,
            ],
        ];
    }
};
