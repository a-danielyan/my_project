<?php

namespace Database\Seeders;

use App\Models\Solutions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SolutionTableSeeder extends Seeder
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
            Solutions::query()->create($status);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseIndustries(): array
    {
        return [
            [
                'name' => 'Solution1',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Solution2',
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
