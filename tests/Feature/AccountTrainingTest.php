<?php

use Tests\TestCase;

class AccountTrainingTest extends TestCase
{
    public function test_the_accounts_demo_returns_a_successful_response(): void
    {
        $response = $this->get(self::ACCOUNT_ROUTE . '/1/training');
        $response->assertStatus(200);
    }

    public function test_get_account_demo(): void
    {
        $response = $this->get(self::ACCOUNT_ROUTE . '/1/training/1');
        $response->assertStatus(200);
    }

    public function test_create_account(): void
    {
        $response = $this->post(self::ACCOUNT_ROUTE . '/1/training', $this->getTestAccountDemoData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'trainingDate']);
        $createdAccount = json_decode($response->getContent());
        $response = $this->get(self::ACCOUNT_ROUTE . '/1/training/' . $createdAccount->id);
        $response->assertStatus(200);
    }

    public function test_update_account(): void
    {
        $response = $this->put(self::ACCOUNT_ROUTE . '/1/training/1', $this->getTestAccountDemoData());
        $response->assertStatus(200)->assertJsonStructure(['id', 'trainingDate']);
    }

    public function test_delete_account(): void
    {
        $response = $this->delete(self::ACCOUNT_ROUTE . '/1/training/1');
        $response->assertStatus(200);
        $response = $this->get(self::ACCOUNT_ROUTE . '/1/training/1');
        $response->assertStatus(404);
    }

    private function getTestAccountDemoData(): array
    {
        return [
            'trainingDate' => fake()->date(),
            'dueDate' => fake()->date(),
            'trainedBy' => 1,
            'note' => fake()->text(),
            'description' => fake()->text(),
            'relatedTo' => 1,
        ];
    }
}
