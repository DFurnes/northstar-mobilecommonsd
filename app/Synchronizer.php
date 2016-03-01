<?php

namespace App;

use App\Services\MobileCommons;
use Carbon\Carbon;
use DoSomething\Northstar\NorthstarClient;
use SimpleXMLElement;

class Synchronizer
{
    /**
     * The Mobile Commons API client.
     *
     * @var MobileCommons
     */
    protected $mobileCommons;

    /**
     * The Northstar API client.
     * @var NorthstarClient
     */
    protected $northstar;

    /**
     * The number of results to fetch per page.
     *
     * @var int
     */
    protected $limit = 100;

    /**
     * Synchronizer constructor.
     * @param MobileCommons $mobileCommons
     * @param NorthstarClient $northstar
     */
    public function __construct(MobileCommons $mobileCommons, NorthstarClient $northstar)
    {
        $this->mobileCommons = $mobileCommons;
        $this->northstar = $northstar;
    }

    /**
     * ...
     */
    public function backfill()
    {
        $profiles = $this->loadResults(Carbon::parse('January 1 1993'), Carbon::now(), 1);

        dd($profiles);
    }

    /**
     * Load profile results between the given start and end dates.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public function loadResults(Carbon $start, Carbon $end, $page)
    {
        $response = $this->mobileCommons->listAllProfiles(
            $start, $end, $this->limit, $page
        );

        $profiles = $response->profiles;

        // Get the number returned from: <profiles num="x">...</profiles>
        // If we hit the limit, chances are there's another page...
        $numReturned = (int) $profiles->attributes()->num;
        if ($numReturned === $this->limit) {
            // @TODO: Dispatch a job to load the rest of the results from this time frame.
        }

        // Serialize the returned profiles into a standard array.
        $serializedProfiles = [];
        foreach ($profiles->children() as $key => $xml) {
            array_push($serializedProfiles, $this->transformProfile($xml));
        }

        return $serializedProfiles;
    }

    /**
     * Transform an XML profile into a plain array for easier processing.
     *
     * @param SimpleXMLElement $profile
     * @return array
     */
    public function transformProfile(SimpleXMLElement $profile)
    {
        return [
            'mobilecommons_id' => (string) $profile->attributes()->id,
            'first_name' => (string) $profile->first_name,
            'phone_number' => (string) $profile->phone_number,
            'status' => (string) $profile->status,
            'created_at' => (string) $profile->created_at,
            'updated_at' => (string) $profile->updated_at,
        ];
    }
}
