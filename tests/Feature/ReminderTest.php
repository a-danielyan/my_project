<?php


use Tests\TestCase;

class ReminderTest extends TestCase
{
    public function test_reminder_list(): void
    {
        $response = $this->get(self::REMINDER_ROUTE);
        $response->assertStatus(200);
    }

    public function test_create_reminder(): void
    {
        $response = $this->post(self::REMINDER_ROUTE, $this->getReminderData());
        $response->assertStatus(200);
    }

    public function test_single_reminder(): void
    {
        $response = $this->get(self::REMINDER_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_update_reminder(): void
    {
        $response = $this->put(self::REMINDER_ROUTE . '/1', $this->getReminderData());
        $response->assertStatus(200);
    }

    public function test_delete_reminder(): void
    {
        $this->beginDatabaseTransaction();
        $response = $this->delete(self::REMINDER_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getReminderData(): array
    {
        return [
            'name' => fake()->text(20),
            'status' => 'Active',
            "relatedEntity" => "Subscription",
            "remindEntity" => "Account",
            "remindDays" => fake()->numberBetween(0, 5),
            "remindType" => "before",
            "sender" => [fake()->email],
            "subject" => fake()->text(150),
            "reminderText" => fake()->randomHtml,
        ];
    }
}
