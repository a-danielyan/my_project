<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MinimalDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for test database.
     */
    public function run(): void
    {
        $this->call(RoleTableSeeder::class);
        $this->call(CustomFieldLeadsSeeder::class);
        $this->call(LeadSourceTableSeeder::class);
        $this->call(LeadStatusTableSeeder::class);
        $this->call(IndustriesTableSeeder::class);
        $this->call(SolutionTableSeeder::class);
        $this->call(StageTableSeeder::class);
        $this->call(LeadTypeTableSeeder::class);
        $this->call(AccountPartnershipTableSeeder::class);
        $this->call(ContactAuthorityTableSeeder::class);
        $this->call(ContactTypeTableSeeder::class);
        $this->call(TagTableSeeder::class);
        $this->call(CustomFieldProductSeeder::class);
        $this->call(CustomFieldEstimateSeeder::class);
        $this->call(CustomFieldContactSeeder::class);
        $this->call(CustomFieldOpportunitySeeder::class);
        $this->call(CustomFieldAccountSeeder::class);
        $this->call(CustomFieldGeneralSeeder::class);
        $this->call(PaymentProfileTableSeeder::class);
    }
}
