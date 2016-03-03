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
        // @TODO: Load next open time frame from the database.
        // @TODO: Then, save ✔ to that record when all jobs for that time frame are done.
        // Save and fetch this from database? POST *should* be idempotent, so
        // there shouldn't be a downside to running a job every X minutes and
        // fetching the last X + 5 minutes of records.
        $start = Carbon::now()->subMinutes(15);
        $end = Carbon::now();

        $this->info('Loading users from '.$start->diffForHumans().' to now...');

        // Sample of 2729 profiles to backfill... 27 pages!
        dispatch(new LoadPaginatedResults($start, $end));
    }
}
