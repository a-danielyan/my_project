<?php

namespace Database\Seeders;

use App\Models\AccountPartnershipStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AccountPartnershipTableSeeder extends Seeder
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
            AccountPartnershipStatus::query()->create($source);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseType(): array
    {
        return [
            [
                'name' => 'Pending Application & NDA',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Pending NDA',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Pending Agreement',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Certified Partner',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Strategic Partner',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Reseller Partner',
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
