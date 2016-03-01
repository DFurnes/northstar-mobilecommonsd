<?php

namespace App\Jobs;

use App\Services\MobileCommons;
use Carbon\Carbon;
use SimpleXMLElement;

class LoadPaginatedResults extends Job
{
    /**
     * Beginning of time frame that we're loading.
     *
     * @var Carbon
     */
    protected $start;

    /**
     * End of time frame that we're loading.
     *
     * @var Carbon
     */
    protected $end;

    /**
     * ...
     *
     * @var int
     */
    protected $page;

    /**
     * Create a new job instance.
     * @param Carbon $start
     * @param Carbon $end
     * @param $page
     */
    public function __construct(Carbon $start, Carbon $end, $page)
    {
        $this->start = $start;
        $this->end = $end;
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @param MobileCommons $mobileCommons
     */
    public function handle(MobileCommons $mobileCommons)
    {
        app('log')->debug('Starting a queued job to load paginated MC results...');

        $response = $mobileCommons->listAllProfiles(
            $this->start, $this->end, $this->page
        );

        $profiles = $response->profiles;

        // Transform the returned profiles to arrays & send to Northstar
        foreach ($profiles->children() as $key => $xml) {
            $user = $this->transformProfile($xml);
            dispatch(new SendUserToNorthstar($user));
        }

        // Get the number returned from: <profiles num="x">...</profiles>
        // If the number returned matches the limit, chances are there's another page...
        $numReturned = (int) $profiles->attributes()->num;
        if ($numReturned === $mobileCommons->getLimit()) {
            app('log')->debug('There\'s more results... kicking off a job for page '.($this->page + 1).'!');
            dispatch(new self($this->start, $this->end, $this->page + 1));
        } else {
            app('log')->debug('That\'s all for now, folks!');
        }
    }

    /**
     * Transform an XML profile into an array for submitting to Northstar.
     *
     * @param SimpleXMLElement $profile
     * @return array
     */
    public function transformProfile(SimpleXMLElement $profile)
    {
        return [
            'first_name' => (string) $profile->first_name,
            'mobile' => (string) $profile->phone_number,
            'mobilecommons_id' => (string) $profile->attributes()->id,
            'mobilecommons_status' => (string) $profile->status,
            'created_at' => Carbon::parse((string) $profile->created_at)->format('Y-m-d'),
        ];
    }
}
