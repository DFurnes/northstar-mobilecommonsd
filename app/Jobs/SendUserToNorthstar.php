<?php

namespace App\Jobs;

use DoSomething\Northstar\NorthstarClient;

class SendUserToNorthstar extends Job
{
    protected $northstar;

    /**
     * Create a new job instance.
     * @param NorthstarClient $northstar
     */
    public function __construct(NorthstarClient $northstar)
    {
        $this->northstar = $northstar;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
