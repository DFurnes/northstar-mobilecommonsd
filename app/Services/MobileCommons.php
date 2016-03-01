<?php

namespace App\Services;

use GuzzleHttp\Client;

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
     * @see <https://mobilecommons.zendesk.com/hc/en-us/articles/202052534-REST-API#ListAllProfiles>
     * @param $start
     * @param $end
     * @param $page
     * @param int $limit
     */
    public function listAllProfiles($start, $end, $page, $limit = 100)
    {
        $response = $this->client->get('profile');

        // @TODO
    }
}
