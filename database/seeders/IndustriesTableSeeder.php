<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class IndustriesTableSeeder extends Seeder
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
            Industry::query()->create($status);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseIndustries(): array
    {
        return [
            [
                'name' => 'ASP (Application Service Provider)',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Data/Telecom OEM',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'ERP (Enterprise Resource Planning)',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Government/Military',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Large Enterprise',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'ManagementISV',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'MSP (Management Service Provider)',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Network Equipment Enterprise',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Non-management ISV',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Optical Networking',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Service Provider',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Small/Medium Enterprise',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Storage Equipment',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Storage Service Provider',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Systems Integrator',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Wireless Industry',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Financial Services',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Education',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Technology',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Real Estate',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Consulting',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Communications',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Manufacturing',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Hotel',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Travel',
                'status' => 'Active',
                'created_by' => 1,
            ],
            [
                'name' => 'Healthcare',
                'status' => 'Active',
                'created_by' => 1,
            ],
        ];
    }
}
