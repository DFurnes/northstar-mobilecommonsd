<?php

namespace App\Console\Commands;

use App\Jobs\LoadPaginatedResults;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FetchUpdatesFromMobileCommons extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'mobilecommons:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start jobs to fetch new/updated records from Mobile Commons and push to Northstar.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // @TODO: Keep track of & load next open time frame from the database.
        // @TODO: Then, save ✔ to that record when all jobs for that time frame are done.
        // The `POST users/` endpoint is idempotent so there *shouldn't* be any
        // downside to just scheduling this with some overlap for a MVP.
        $start = Carbon::now()->subMinutes(10);
        $end = Carbon::now();

        $this->info('Loading users from '.$start->diffForHumans().' to now...');

        dispatch(new LoadPaginatedResults($start, $end));
    }
}
