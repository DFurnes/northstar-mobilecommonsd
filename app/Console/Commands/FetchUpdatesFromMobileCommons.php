<?php

namespace App\Console\Commands;

use App\Services\MobileCommons;
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
    protected $description = 'Fetch new/updated records from Mobile Commons and push to Northstar.';

    /**
     * Create FetchUpdatesFromMobileCommons command.
     * @param MobileCommons $mobileCommons
     */
    public function __construct(MobileCommons $mobileCommons)
    {
        $this->mobileCommons = $mobileCommons;

        parent::__construct();
    }

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
