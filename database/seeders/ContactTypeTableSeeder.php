<?php

namespace Database\Seeders;

use App\Models\ContactType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ContactTypeTableSeeder extends Seeder
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
            ContactType::query()->create($source);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseType(): array
    {
        return [
            [
                'name' => 'End User',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Referral Agent',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Reseller Partner',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Ad Network Operator',
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
