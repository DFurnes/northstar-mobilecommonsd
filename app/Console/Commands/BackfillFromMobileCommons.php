<?php

namespace App\Console\Commands;

use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\LoadResultsFromMobileCommons;

class BackfillFromMobileCommons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mobilecommons:backfill
                           {start?} {end?}
                           {--reset : Force a new backfill job.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill profile data from Mobile Commons into Northstar.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Starting new backfill process...');

        if (is_null($start = $this->argument('start'))) {
            $start = $this->ask('When should the backfill start?', '4/1/2009');
        }

        if (is_null($end = $this->argument('end'))) {
            $end = $this->ask('When should the backfill end?', 'now');
        }

        // Split the time range into week-long chunks that can be processed in parallel.
        $interval = new DateInterval('P7D');
        $periods = collect(new DatePeriod(Carbon::parse($start), $interval, Carbon::parse($end)));

        $this->comment('Queuing jobs to load segments from MobileCommons:');
        $this->output->progressStart($periods->count());

        foreach ($periods as $start) {
            $this->output->progressAdvance();

            $end = Carbon::parse($start)->add($interval);
            dispatch(new LoadResultsFromMobileCommons($start, $end));
        }

        $this->info(PHP_EOL . '✔ Jobs created!');
    }
}
