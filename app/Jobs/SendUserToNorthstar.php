<?php

namespace App\Jobs;

use DoSomething\Northstar\NorthstarClient;
use Illuminate\Queue\InteractsWithQueue;

class SendUserToNorthstar extends Job
{
    use InteractsWithQueue;

    /**
     * Mobile Commons User
     *
     * @var array
     */
    protected $user;

    /**
     * Create a new job instance.
     * @param array $user
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param NorthstarClient $northstar
     */
    public function handle(NorthstarClient $northstar)
    {
        //
    }
}
