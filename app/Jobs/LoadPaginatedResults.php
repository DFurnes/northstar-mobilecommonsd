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
    public function __construct(Carbon $start, Carbon $end, $page = 1)
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
        $payload = [
            'first_name' => (string) $profile->first_name,
            'mobile' => (string) $profile->phone_number,
            'mobilecommons_id' => (string) $profile->attributes()->id,
            'mobilecommons_status' => $this->transformStatus($profile->status),
            'source' => $this->transformSource($profile->source),
            'created_at' => Carbon::parse((string) $profile->created_at)->format('Y-m-d'),
        ];

        // Return transformed payload, excluding any blank fields.
        return array_filter($payload);
    }

    /**
     * Transform the contents of the `<profile><status>...</status></profile>` field.
     *
     * @param SimpleXMLElement[] $status
     * @return string
     */
    public function transformStatus($status)
    {
        // @see: https://mobilecommons.zendesk.com/hc/en-us/articles/202052284-Profiles
        $statusTokens = [
            'Undeliverable' => 'undeliverable', // Phone number can't receive texts
            'Hard bounce' => 'undeliverable', // Invalid mobile number
            'No Subscriptions' => 'opted_out', // User is not opted in to any MC campaigns
            'Texted a STOP word' => 'opted_out', // User opted-out by texting STOP
            'Active Subscriber' => 'active',
        ];

        // Map to normalized status keywords, or 'undeliverable' on unknown status
        return array_get($statusTokens, (string) $status, 'undeliverable');
    }

    /**
     * Transform the contents of the `<profile><source ... /></profile>` field.
     *
     * @param SimpleXMLElement[] $source
     * @return string
     */
    public function transformSource($source)
    {
        // @see: https://mobilecommons.zendesk.com/hc/en-us/articles/202641890-Glossary
        $sourceTokens = [
            'Opt-In Path' => 'opt_in_path',
            'Keyword' => 'keyword',
            'Broadcast' => 'broadcast',
            'Tell A Friend' => 'referral',
            'mData' => 'mdata',
        ];

        $type = array_get($sourceTokens, (string) $source->attributes()->type, 'other');
        $id = (string) $source->attributes()->id;
        $formattedId = $type !== 'other' ? '/'.$id : '';

        // e.g. 'mobilecommons:opt_in_path/4701'
        return 'mobilecommons:'.$type.$formattedId;
    }
}
