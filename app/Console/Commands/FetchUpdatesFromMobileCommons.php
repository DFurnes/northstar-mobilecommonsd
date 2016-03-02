<?php

namespace App\Console\Commands;

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
        $this->info('Under construction!');
    }
}
