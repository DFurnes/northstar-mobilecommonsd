<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Services\MobileCommons;

class LoadResultsFromMobileCommons extends Job
{
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'nsmcd-mobilecommons';

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
     * The current page of results.
     *
     * @var int
     */
    protected $page;

    /**
     * Create a new job instance.
     * @param Carbon $start
     * @param Carbon $end
     * @param int $page
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
        $response = $mobileCommons->listAllProfiles($this->start, $this->end, $this->page);
        app('log')->debug('Loaded page from MobileCommons: '.$this->start.' to '.$this->end.' ('.$this->page.')');

        // Transform the returned profiles to arrays & send to Northstar
        foreach ($response->profiles->children() as $key => $profile) {
            app('log')->debug('Queued Northstar job for '.$profile->attributes()->id.' from '.$this->start.'-'.$this->end.' page '.$this->page.'.');

            // Remove extra markup from the XML so we don't have messages that won't "fit" in the queue.
            unset($profile->address, $profile->custom_columns, $profile->location, $profile->clicks, $profile->integrations);

            $xml = (string) $profile->asXML();
            if (strlen($xml) > 262144) {
                app('log')->warning('Long XML payload queued for '.$profile->attributes()->id, ['xml' => $xml]);
            }

            dispatch(new SendUserToNorthstar($xml));
        }

        // Get the number returned from: <profiles num="x">...</profiles>
        // If the number returned matches the limit, chances are there's another page...
        $numReturned = (int) $response->profiles->attributes()->num;
        $done = $numReturned !== $mobileCommons->getLimit();

        if (! $done) {
            dispatch(new self($this->start, $this->end, $this->page + 1));
        }

        app('db')->table('progress')
            ->updateOrInsert(['start' => $this->start, 'end' => $this->end], ['page' => $this->page, 'done' => $done]);
    }
}
