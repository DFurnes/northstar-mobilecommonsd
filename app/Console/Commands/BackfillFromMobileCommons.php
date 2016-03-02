<?php

namespace App\Console\Commands;

use App\Jobs\LoadPaginatedResults;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BackfillFromMobileCommons extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'mobilecommons:backfill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start jobs to backfill existing records from Mobile Commons into Northstar.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting backfill process...');

        // Sample of 2729 profiles to backfill... 27 pages!
        dispatch(new LoadPaginatedResults(Carbon::parse('April 1 2009'), Carbon::parse('April 3 2009'), 25));
    }
}
