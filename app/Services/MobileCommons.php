<?php

namespace App\Services;

use GuzzleHttp\Client;
use Carbon\Carbon;

class MobileCommons
{
    /**
     * The HTTP client.
     * @var Client
     */
    protected $client;

    /**
     * Make a new MobileCommons API client.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->client = new Client([
            'base_url' => 'https://secure.mcommons.com/api/',
            'defaults' => [
                'auth' => [$config['username'], $config['password']],
            ],
        ]);
    }

    /**
     * List all profiles changed within the given time frame.
     *
     * Gotcha: Mobile Commons will return the number of elements in *this* XML response,
     * not the total matching the query. So, if <profiles num=$limit>, then you should
     * ask for the next page... yeah.
     *
     * @see <https://mobilecommons.zendesk.com/hc/en-us/articles/202052534-REST-API#ListAllProfiles>
     * @param Carbon $start
     * @param Carbon $end
     * @param int $limit
     * @param int $page
     * @return \SimpleXMLElement
     */
    public function listAllProfiles($start, $end, $limit = 50, $page = 1)
    {
        $response = $this->client->get('profiles', [
            'query' => [
                'start' => $start->toIso8601String(),
                'end' => $end->toISO8601String(),
                'limit' => $limit,
                'page' => $page,
            ],
        ]);

        return $response->xml();
    }
}
