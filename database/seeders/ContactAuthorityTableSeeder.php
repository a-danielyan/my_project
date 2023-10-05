<?php

namespace Database\Seeders;

use App\Models\ContactAuthority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ContactAuthorityTableSeeder extends Seeder
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
            ContactAuthority::query()->create($source);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseType(): array
    {
        return [
            [
                'name' => 'Decision Maker',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Accounts Payable',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Influencer',
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
