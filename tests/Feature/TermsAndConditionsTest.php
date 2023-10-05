<?php

use App\Models\TermsAndConditions;
use Tests\TestCase;

class TermsAndConditionsTest extends TestCase
{
    /**
     * @return void
     */
    public function test_terms_returns_a_successful_response(): void
    {
        $response = $this->get(self::TERMS_AND_CONDITIONS_ROUTE . '?entity=' . TermsAndConditions::INVOICE_ENTITY);
        $response->assertStatus(200);
    }

    public function test_create_terms_preference(): void
    {
        $response = $this->post(self::TERMS_AND_CONDITIONS_ROUTE, $this->getTestData());
        $response->assertStatus(422);

        TermsAndConditions::query()->where('entity', TermsAndConditions::ESTIMATE_ENTITY)->forceDelete();
        $response = $this->post(self::TERMS_AND_CONDITIONS_ROUTE, $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_update_terms(): void
    {
        $response = $this->put(self::TERMS_AND_CONDITIONS_ROUTE . '/1', $this->getTestData());
        $response->assertStatus(200);
    }

    public function test_delete_preference(): void
    {
        $response = $this->delete(self::TERMS_AND_CONDITIONS_ROUTE . '/1');
        $response->assertStatus(200);
    }

    private function getTestData(): array
    {
        return [
            'entity' => TermsAndConditions::ESTIMATE_ENTITY,
            'termsAndCondition' => fake()->randomHtml,
        ];
    }
}
