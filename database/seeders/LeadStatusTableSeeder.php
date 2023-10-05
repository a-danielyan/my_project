<?php

namespace Database\Seeders;

use App\Models\LeadStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LeadStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->getBaseStatuses() as $status) {
            LeadStatus::query()->create($status);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseStatuses(): array
    {
        return [
            [
                'name' => 'Attempted to Contact',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Contact in Future',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Contacted',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Junk Lead',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Lost Lead',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Not Contacted',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Pre Qualified',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Not Qualified',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => LeadStatus::STATUS_CONVERTED,
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
