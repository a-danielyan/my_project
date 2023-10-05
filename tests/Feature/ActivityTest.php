<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Activity;
use App\Models\Tag;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    /**
     * @dataProvider geSortingList
     * @param string $input
     * @return void
     */
    public function test_activity_returns_a_successful_response(string $input): void
    {
        $response = $this->get(self::ACTIVITY_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1');
        $response->assertStatus(200);
    }

    /**
     * @dataProvider geSortingList
     * @param string $input
     * @return void
     */
    public function test_activity_returns_a_successful_response_with_search(string $input): void
    {
        $this->markTestSkipped('Currently we dont use search. Maybe need later');
        $searchString = fake()->text(10);
        $response = $this->get(
            self::ACTIVITY_ROUTE . '?limit=10&sort=' . $input . '&order=desc&page=1&search=' . $searchString,
        );
        $response->assertStatus(200);
    }

    public function test_get_activity(): void
    {
        $response = $this->get(self::ACTIVITY_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_create_activity(): void
    {
        $response = $this->post(self::ACTIVITY_ROUTE, $this->getTestActivityData());
        $response->assertStatus(200);
    }

    public function test_update_activity(): void
    {
        $response = $this->put(self::ACTIVITY_ROUTE . '/1', $this->getTestActivityData());
        $response->assertStatus(200);
    }

    public function test_delete_activity(): void
    {
        $response = $this->delete(self::ACTIVITY_ROUTE . '/2');
        $response->assertStatus(200);
        $response = $this->get(self::ACTIVITY_ROUTE . '/2');
        $response->assertStatus(404);
    }


    private function getTestActivityData(): array
    {
        return [
            'relatedTo' => '1',
            'startedAt' => now(),
            'activityType' => fake()->randomElement(Activity::ACTIVITY_TYPES),
            'activityStatus' => fake()->randomElement(Activity::ACTIVITY_STATUSES),
            'priority' => fake()->randomElement(Activity::PRIORITY_STATUSES),
            'subject' => 'Test subject',
            'relatedToEntity' => 'Lead',
            'dueDate' => date('Y-m-d'),
            'relatedToId' => 1,
            'tag' => [
                [
                    'id' => Tag::query()->where('entity_type', Activity::class)->first()->getKey(),
                ],
            ],
        ];
    }

    public static function geSortingList(): array
    {
        return [
            ['started_at'],
            ['created_at'],
            ['status'],
            ['subject'],
            ['activityType'],
            ['priority'],
            ['activityStatus'],
            ['relatedTo'],
            ['dueDate'],
            ['tag'],
        ];
    }
}
