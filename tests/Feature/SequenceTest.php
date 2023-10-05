<?php


use Tests\TestCase;

class SequenceTest extends TestCase
{
    public function test_sequence_list(): void
    {
        $response = $this->get(self::SEQUENCE_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_sequence(): void
    {
        $response = $this->post(self::SEQUENCE_ROUTE, $this->getsequenceData());
        $response->assertStatus(200);
    }

    public function test_single_sequence(): void
    {
        $response = $this->get(self::SEQUENCE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_sequence(): void
    {
        $response = $this->put(self::SEQUENCE_ROUTE . '/1', $this->getsequenceData());
        $response->assertStatus(200);
    }

    public function test_delete_sequence(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::SEQUENCE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getsequenceData(): array
    {
        return [
            'name' => fake()->text(20),
            "startDate" => fake()->date(),
            "isActive" => true,
            "templates" => [
                [
                    "templateId" => 1,
                    "sendAfter" => 5,
                    "sendAfterUnit" => "day",
                ],
            ],
            "entity" => [
                [
                    "type" => "Lead",
                    "id" => 1,
                ],
            ],
        ];
    }
}
