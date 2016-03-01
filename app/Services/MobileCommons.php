<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use SimpleXMLElement;
use Exception;

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
            'base_uri' => 'https://secure.mcommons.com/api/profile',
            'auth' => [$config['username'], $config['password']],
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
        // @TODO
    }


    /**
     * Parse XML responses into an associative array. Plucked from the
     * remnants of the pre-PSR7 Guzzle. RIP.
     *
     * @param Response $response
     * @return SimpleXMLElement|null
     * @throws Exception
     */
    public function parseXml($response)
    {
        $disableEntities = libxml_disable_entity_loader(true);
        $internalErrors = libxml_use_internal_errors(true);

        // Allow XML to be retrieved even if there is no response body
        $body = (string) $response->getBody() ?: '<root />';

        try {
            $xml = new SimpleXMLElement($body, LIBXML_NONET);
        } catch (Exception $e) {
            throw new Exception('Unable to parse response body into XML: '.$e->getMessage());
        } finally {
            libxml_disable_entity_loader($disableEntities);
            libxml_use_internal_errors($internalErrors);
        }

        return isset($xml) ? $xml : null;
    }
}
