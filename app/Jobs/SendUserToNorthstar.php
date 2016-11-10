<?php

namespace App\Jobs;

use Carbon\Carbon;
use DoSomething\Northstar\Exceptions\APIException;
use DoSomething\Northstar\NorthstarClient;
use SimpleXMLElement;

class SendUserToNorthstar extends Job
{
    /**
     * Mobile Commons profile result.
     *
     * @var string
     */
    protected $profile;

    /**
     * Create a new job instance.
     * @param string $profile
     */
    public function __construct($profile)
    {
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @param NorthstarClient $northstar
     */
    public function handle(NorthstarClient $northstar)
    {
        $xml = new SimpleXMLElement($this->profile);
        $user = $this->transformProfile($xml);
        $mc_id = (string) $user['mobilecommons_id'];

        try {
            $northstarUser = $northstar->createUser($user);

            app('log')->debug('Sent user '.$mc_id.' to NS... saved to '.$northstarUser->id.'!');
        } catch (APIException $e) {
            app('log')->error('Encountered error saving user '.$mc_id.' to NS.', ['error' => $e]);
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
            'created_at' => Carbon::parse((string) $profile->created_at)->toIso8601String(),
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
            'Hard bounce' => 'bounce', // Invalid mobile number
            'No Subscriptions' => 'no_subscriptions', // User is not opted in to any MC campaigns
            'Texted a STOP word' => 'opted_out', // User opted-out by texting STOP
            'Active Subscriber' => 'active',
        ];

        // Map to normalized status keywords, or 'unknown' on unknown status
        return array_get($statusTokens, (string) $status, 'unknown');
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
            'Tell A Friend' => 'tell_a_friend',
            'mData' => 'mdata',
            'Unknown' => 'unknown',
        ];

        $type = array_get($sourceTokens, (string) $source->attributes()->type, 'other');
        $id = (string) $source->attributes()->id;

        // e.g. 'mobilecommons:opt_in_path/4701'
        return 'mobilecommons:'.$type.(! empty($id) ? '/' : '').$id;
    }
}
