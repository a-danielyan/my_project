<?php


use Tests\TestCase;

class SubjectLineTest extends TestCase
{
    public function test_subject_line_list(): void
    {
        $response = $this->get(self::SUBJECT_LINE_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_subject_line(): void
    {
        $response = $this->post(self::SUBJECT_LINE_ROUTE, $this->getCreateData());
        $response->assertStatus(200);
    }

    public function test_single_subject_line(): void
    {
        $response = $this->get(self::SUBJECT_LINE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_subject_line(): void
    {
        $response = $this->put(self::SUBJECT_LINE_ROUTE . '/1', $this->getCreateData());
        $response->assertStatus(200);
    }

    public function test_delete_subject_line(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::SUBJECT_LINE_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getCreateData(): array
    {
        return [
            'subjectText' => fake()->text(20),
        ];
    }
}
