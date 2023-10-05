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
        foreach ($this->listFieldsForDelete() as $field) {
            CustomField::query()->where('entity_type', $field['entity_type'])
                ->where('code', $field['code'])->forceDelete();
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

    private function listFieldsForDelete(): array
    {
        return [
            ['entity_type' => Contact::class, 'code' => 'apollo_id'],
            ['entity_type' => Lead::class, 'code' => 'apollo_id'],

            ['entity_type' => Contact::class, 'code' => 'apollo_title'],
            ['entity_type' => Lead::class, 'code' => 'apollo_title'],

            ['entity_type' => Contact::class, 'code' => 'twitter_url'],
            ['entity_type' => Lead::class, 'code' => 'twitter_url'],

            ['entity_type' => Contact::class, 'code' => 'github_url'],
            ['entity_type' => Lead::class, 'code' => 'github_url'],

            ['entity_type' => Contact::class, 'code' => 'facebook_url'],
            ['entity_type' => Lead::class, 'code' => 'facebook_url'],

            ['entity_type' => Contact::class, 'code' => 'headline'],
            ['entity_type' => Lead::class, 'code' => 'headline'],

            ['entity_type' => Contact::class, 'code' => 'apollo_contact_id'],
            ['entity_type' => Lead::class, 'code' => 'apollo_contact_id'],

            ['entity_type' => Contact::class, 'code' => 'apollo_contact'],
            ['entity_type' => Lead::class, 'code' => 'apollo_contact'],

            ['entity_type' => Contact::class, 'code' => 'apollo_organization_id'],
            ['entity_type' => Lead::class, 'code' => 'apollo_organization_id'],

            ['entity_type' => Contact::class, 'code' => 'apollo_organization'],
            ['entity_type' => Lead::class, 'code' => 'apollo_organization'],

            ['entity_type' => Contact::class, 'code' => 'seniority'],
            ['entity_type' => Lead::class, 'code' => 'seniority'],

            ['entity_type' => Contact::class, 'code' => 'personal_emails'],
            ['entity_type' => Lead::class, 'code' => 'personal_emails'],

            ['entity_type' => Contact::class, 'code' => 'departments'],
            ['entity_type' => Lead::class, 'code' => 'departments'],

            ['entity_type' => Contact::class, 'code' => 'subdepartments'],
            ['entity_type' => Lead::class, 'code' => 'subdepartments'],

            ['entity_type' => Contact::class, 'code' => 'functions'],
            ['entity_type' => Lead::class, 'code' => 'functions'],


            ['entity_type' => Account::class, 'code' => 'apollo_id'],
            ['entity_type' => Account::class, 'code' => 'blog_url'],
            ['entity_type' => Account::class, 'code' => 'angellist_url'],
            ['entity_type' => Account::class, 'code' => 'twitter_url'],
            ['entity_type' => Account::class, 'code' => 'facebook_url'],
            ['entity_type' => Account::class, 'code' => 'languages'],
            ['entity_type' => Account::class, 'code' => 'alexa_ranking'],
            ['entity_type' => Account::class, 'code' => 'linkedin_uid'],
            ['entity_type' => Account::class, 'code' => 'founded_year'],
            ['entity_type' => Account::class, 'code' => 'logo_url'],
            ['entity_type' => Account::class, 'code' => 'crunchbase_url'],
            ['entity_type' => Account::class, 'code' => 'primary_domain'],
            ['entity_type' => Account::class, 'code' => 'persona_counts'],
            ['entity_type' => Account::class, 'code' => 'keywords'],
            ['entity_type' => Account::class, 'code' => 'estimated_num_employees'],
            ['entity_type' => Account::class, 'code' => 'suborganizations'],
            ['entity_type' => Account::class, 'code' => 'seo_description'],
            ['entity_type' => Account::class, 'code' => 'annual_revenue'],
            ['entity_type' => Account::class, 'code' => 'total_funding'],
            ['entity_type' => Account::class, 'code' => 'latest_funding_round_date'],
            ['entity_type' => Account::class, 'code' => 'latest_funding_stage'],
            ['entity_type' => Account::class, 'code' => 'funding_events'],
            ['entity_type' => Account::class, 'code' => 'account_id'],
            ['entity_type' => Account::class, 'code' => 'account'],

        ];
    }
};
