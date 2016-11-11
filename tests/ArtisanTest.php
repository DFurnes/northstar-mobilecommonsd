<?php

use App\Jobs\LoadResultsFromMobileCommons;

class ArtisanTest extends TestCase
{
    /**
     * Test that `mobilecommons:backfill` triggers the queue job.
     */
    public function testBackfillCommand()
    {
        $this->expectsJobs(LoadResultsFromMobileCommons::class);
        $this->artisan('mobilecommons:backfill', ['start' => '4/1/2009', 'end' => '4/1/2010']);
    }

    /**
     * Test that `mobilecommons:fetch` triggers the queue job.
     */
    public function testFetchCommand()
    {
        $this->expectsJobs(LoadResultsFromMobileCommons::class);
        $this->artisan('mobilecommons:fetch');
    }
}
