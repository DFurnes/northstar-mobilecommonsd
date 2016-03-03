<?php

use App\Jobs\LoadPaginatedResults;
use DoSomething\Northstar\Resources\NorthstarUser;
use Carbon\Carbon;

class JobTest extends TestCase
{
    /**
     * Test that the "load paginated results" job correctly queues
     * `SendUserToNorthstar` job for each user, and queues itself
     * if it sees that there are more pages.
     */
    public function testQueuingJobs()
    {
        $mobileCommons = $this->mock(\App\Services\MobileCommons::class);

        // For testing, let's assume we're asking for 5 results per page.
        $mobileCommons->shouldReceive('getLimit')->andReturn(5);

        // If there are 6 profiles, MobileCommons will first return a "full" page of results, and then a partially full one.
        $mobileCommons->shouldReceive('listAllProfiles')->once()->andReturn(new SimpleXMLElement(file_get_contents(__DIR__.'/fixtures/profiles.xml')));
        $mobileCommons->shouldReceive('listAllProfiles')->once()->andReturn(new SimpleXMLElement(file_get_contents(__DIR__.'/fixtures/profiles_last.xml')));

        // Each profile will trigger a `SendUserToNorthstar` job which posts to NS.
        $northstarMock = $this->mock(\DoSomething\Northstar\NorthstarClient::class);
        $northstarMock->shouldReceive('createUser')->times(6)->andReturn(new NorthstarUser([
            'id' => '5555abc1ef55551234567890',
        ]));

        // And.... go!
        dispatch(new LoadPaginatedResults(Carbon::now()->subMinutes(10), Carbon::now()));
    }
}
