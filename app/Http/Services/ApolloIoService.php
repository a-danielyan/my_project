<?php

namespace App\Http\Services;

use App\Exceptions\ApolloRateLimitErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class ApolloIoService
{
    public function __construct()
    {
    }

    /**
     * @param string $email
     * @return array
     * @throws ApolloRateLimitErrorException
     * @throws GuzzleException
     */
    public function getPeopleByEmail(string $email): array
    {
        $url = 'https://api.apollo.io/v1/people/match';

        $client = new Client(['timeout' => 10.0]);

        $response = $client->post($url, [
            'form_params' => [
                'api_key' => config('services.apollo.api_key'),
                'email' => $email,
            ],
        ]);

        $result = $response->getBody();
        $this->checkRateLimits($response);


        return json_decode($result, true);
    }

    /**
     * @param string $domain
     * @return array
     * @throws ApolloRateLimitErrorException
     * @throws GuzzleException
     */
    public function getOrganizationByDomain(string $domain): array
    {
        $url = 'https://api.apollo.io/v1/organizations/enrich';

        $client = new Client(['timeout' => 10.0]);

        $response = $client->post($url, [
            'form_params' => [
                'api_key' => config('services.apollo.api_key'),
                'domain' => $domain,
            ],
        ]);

        $result = $response->getBody();
        $this->checkRateLimits($response);

        return json_decode($result, true);
    }


    /**
     * @param string $organizationDomain
     * @param int $page
     * @param array $personTitles
     * @return string
     * @throws ApolloRateLimitErrorException
     * @throws GuzzleException
     */
    public function searchPeople(string $organizationDomain, int $page, array $personTitles): string
    {
        $url = 'https://api.apollo.io/v1/mixed_people/search';

        $client = new Client(['timeout' => 10.0]);

        $response = $client->post($url, [
            'form_params' => [
                'api_key' => config('services.apollo.api_key'),
                'q_organization_domains' => $organizationDomain,
                'page' => $page,
                'person_titles' => $personTitles,
            ],
        ]);
        $this->checkRateLimits($response);

        return (string)$response->getBody();
    }

    /**
     * @param string $queryKeywords
     * @return string
     * @throws ApolloRateLimitErrorException
     * @throws GuzzleException
     */
    public function searchContact(string $queryKeywords): string
    {
        $url = 'https://api.apollo.io/v1/contacts/search';

        $client = new Client(['timeout' => 10.0]);

        $response = $client->post($url, [
            'form_params' => [
                'api_key' => config('services.apollo.api_key'),
                'q_keywords' => $queryKeywords,
                'sort_by_field' => 'contact_last_activity_date',
                'sort_ascending' => false,
            ],
        ]);
        $this->checkRateLimits($response);

        return (string)$response->getBody();
    }

    /**
     * @param string $queryKeywords
     * @return string
     * @throws ApolloRateLimitErrorException
     * @throws GuzzleException
     */
    public function searchAccount(string $queryKeywords): string
    {
        $url = 'https://api.apollo.io/v1/accounts/search';

        $client = new Client(['timeout' => 10.0]);

        $response = $client->post($url, [
            'form_params' => [
                'api_key' => config('services.apollo.api_key'),
                'q_organization_name' => $queryKeywords,
                'sort_by_field' => 'account_last_activity_date',
                'sort_ascending' => false,
            ],
        ]);
        $this->checkRateLimits($response);

        return (string)$response->getBody();
    }

    /**
     * @param Response $response
     * @return void
     * @throws ApolloRateLimitErrorException
     */
    private function checkRateLimits(Response $response): void
    {
        $headers = $response->getHeaders();

        $leftRequestDaily = (int)$headers['x-24-hour-requests-left'][0] ?? null;
        $leftRequestHourly = (int)$headers['x-hourly-requests-left'][0] ?? null;

        if ($leftRequestHourly < 5) {
            throw new ApolloRateLimitErrorException('Hourly rate limit reached');
        }
        if ($leftRequestDaily < 24) {
            throw new ApolloRateLimitErrorException('Daily rate limit reached');
        }
    }
}
