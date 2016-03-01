<?php

namespace App\Console\Commands;

use App\Services\MobileCommons;
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
    protected $description = 'Backfill existing records from Mobile Commons into Northstar.';

    /**
     * The Mobile Commons API client.
     *
     * @var MobileCommons
     */
    protected $mobileCommons;

    /**
     * Create BackfillFromMobileCommons command.
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
