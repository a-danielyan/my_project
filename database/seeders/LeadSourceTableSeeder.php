<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LeadSourceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->getBaseSource() as $source) {
            LeadSource::query()->create($source);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseSource(): array
    {
        return [
            [
                'name' => 'Advertisement',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Cold Call',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Employee Referral',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'External Referral',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'OnlineStore',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Partner',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Public Relations',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Sales Mail Alias',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Seminar Partner',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Seminar-Internal',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Trade Show',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Web Download',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Web Research',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Chat',
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
