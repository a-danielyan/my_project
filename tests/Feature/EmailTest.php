<?php

use App\Http\Services\EmailService;
use App\Models\Email;
use App\Models\OauthToken;
use Mockery\MockInterface;
use Tests\TestCase;

class EmailTest extends TestCase
{
    private int $tokenId;

    protected function setUp(): void
    {
        parent::setUp();
        $token = OauthToken::query()->where('service', 'Gmail')->first();
        $this->tokenId = $token->getKey();
    }

    public function test_get_emails(): void
    {
        $response = $this->get(self::EMAIL_ROUTE . '?tokenId=' . $this->tokenId);
        $response->assertStatus(200);
    }

    public function test_get_single_email(): void
    {
        $response = $this->get(self::EMAIL_ROUTE . '/1');
        $response->assertStatus(200);
    }

    public function test_send_email(): void
    {
        $this->mock(
            EmailService::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('send')->andReturn(new Email());
            },
        );

        $response = $this->post(self::EMAIL_ROUTE, $this->getTestEmailData());
        $response->assertStatus(200);
    }

    private function getTestEmailData(): array
    {
        return [
            'subject' => 'Test subject',
            'message' => 'Test message body',
            'sendTo' => ['work123work123@gmail.com'],
            'tokenId' => $this->tokenId,
        ];
    }
}
