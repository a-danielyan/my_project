<?php

namespace Database\Seeders;

use App\Models\Lead;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Can be used for seed Database with test items
     * @return void
     */
    public function run(): void
    {
        Lead::factory()->count(5000)->create();
    }
}
