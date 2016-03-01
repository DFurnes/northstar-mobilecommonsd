<?php

namespace App\Console\Commands;

use App\Synchronizer;
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
     * The synchronizer.
     * @var Synchronizer
     */
    protected $synchronizer;

    /**
     * Create FetchUpdatesFromMobileCommons command.
     * @param Synchronizer $synchronizer
     */
    public function __construct(Synchronizer $synchronizer)
    {
        $this->synchronizer = $synchronizer;

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
