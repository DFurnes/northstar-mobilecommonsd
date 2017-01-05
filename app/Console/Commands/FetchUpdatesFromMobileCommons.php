<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\LoadResultsFromMobileCommons;

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
     * @return void
     */
    public function handle()
    {
        $start = Carbon::now()->subMinutes(10);
        $end = Carbon::now();

        dispatch(new LoadResultsFromMobileCommons($start, $end));
    }
}
