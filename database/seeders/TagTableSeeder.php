<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->getBaseTag() as $source) {
            Tag::query()->create($source);
        }
        Schema::enableForeignKeyConstraints();
    }

    private function getBaseTag(): array
    {
        $tagData = [];

        foreach (Tag::AVAILABLE_ENTITY as $entity) {
            $tagData[] = [
                'tag' => 'Default',
                'entity_type' => 'App\Models\\' . $entity,
                'created_by' => 1,
            ];
        }

        return $tagData;
    }
}
