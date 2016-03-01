<?php

namespace App\Console\Commands;

use App\Synchronizer;
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
     * The synchronizer.
     * @var Synchronizer
     */
    protected $synchronizer;

    /**
     * Create BackfillFromMobileCommons command.
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

        $this->synchronizer->backfill();
    }
}
