<?php

use App\Jobs\LoadPaginatedResults;

class ArtisanTest extends TestCase
{
    /**
     * Test that `mobilecommons:backfill` triggers the queue job.
     */
    public function testBackfillCommand()
    {
        $this->expectsJobs(LoadPaginatedResults::class);
        $this->artisan('mobilecommons:backfill');
    }

    /**
     * Test that `mobilecommons:fetch` triggers the queue job.
     */
    public function testFetchCommand()
    {
        $this->expectsJobs(LoadPaginatedResults::class);
        $this->artisan('mobilecommons:fetch');
    }
}
