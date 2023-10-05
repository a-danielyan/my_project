<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->getBaseIndustries() as $status) {
            Stage::query()->create($status);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseIndustries(): array
    {
        return [
            [
                'name' => 'Qualification',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Needs Analysis',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Value Proposition',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Identify Decision Makers',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Proposal/Price Quote',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Negotiation/Review',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => Stage::CLOSED_WON_STAGE,
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => Stage::CLOSED_LOST_STAGE,
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
