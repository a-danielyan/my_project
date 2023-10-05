<?php

namespace Tests;

use Throwable;

trait AuthTrait
{
    protected string $routeLogin = '/api/v1/socialLogin';
    protected string $defaultUsername;

    /**
     * Add auth data to headers by credentials, if credential not set use default user
     *
     * @param string|null $username
     * @return void
     * @throws Throwable
     */
    public function authorize(?string $username = null): void
    {
        // If authorize data not set use defaultUser credential
        if ($username === null) {
            $username = $this->defaultUsername;
        }

        // Send login request
        $response = $this->postJson($this->routeLogin, [
            'provider' => 'local',
            'token' => $username,
        ]);

        // Check if response is valid
        $response->assertOk();
        $response->assertJsonStructure([
            'accessToken',
            'tokenType',
            'expiresIn',
        ]);

        $responseData = $response->decodeResponseJson();

        // Set headers
        $this->withHeader(
            'Authorization',
            $responseData['tokenType'] . ' ' . $responseData['accessToken'],
        );
    }
}
