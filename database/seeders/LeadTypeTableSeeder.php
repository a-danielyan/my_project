<?php

namespace Database\Seeders;

use App\Models\LeadType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LeadTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->getBaseType() as $source) {
            LeadType::query()->create($source);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseType(): array
    {
        return [
            [
                'name' => 'Hot lead',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Warm lead',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Cold lead',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Information qualified lead (IQL)',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Sales qualified lead (SQL)',
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
