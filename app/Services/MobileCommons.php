<?php

namespace App\Services;

use GuzzleHttp\Client;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use SimpleXMLElement;

class MobileCommons
{
    /**
     * The HTTP client.
     * @var Client
     */
    protected $client;

    /**
     * The number of results to fetch per page.
     *
     * @var int
     */
    protected $limit = 100;

    /**
     * Make a new MobileCommons API client.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->client = new Client([
            'base_uri' => 'https://secure.mcommons.com/api/',
            'auth' => [$config['username'], $config['password']],
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
     * @param int $page
     * @return SimpleXMLElement
     */
    public function listAllProfiles($start = null, $end = null, $page = 1)
    {
        $query = [
            'limit' => $this->getLimit(),
            'page' => $page,
        ];

        if (! is_null($start)) {
            $query['from'] = $start->toIso8601String();
        }

        if (! is_null($end)) {
            $query['to'] = $end->toIso8601String();
        }

        $response = $this->client->get('profiles', [
            'query' => $query,
        ]);

        return $this->parseXml($response);
    }

    /**
     * Return the current limit being fetched per page.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Parse XML from PSR-7 responses.
     *
     * @param Response $response
     * @return SimpleXMLElement
     */
    public function parseXml(Response $response)
    {
        return new SimpleXMLElement((string) $response->getBody() ?: '<root />', LIBXML_NONET);
    }
}
