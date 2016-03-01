<?php

namespace App\Jobs;

use DoSomething\Northstar\Exceptions\APIException;
use DoSomething\Northstar\NorthstarClient;

class SendUserToNorthstar extends Job
{
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
        $number = (string) $this->user['mobile'];
        app('log')->debug('Grabbed profile for: '.$number);

        try {
            $northstarUser = $northstar->createUser($this->user);

            app('log')->debug('Sent user '.$number.' to NS... saved to '.$northstarUser->id.'!');
        } catch (APIException $e) {
            app('log')->error('Encountered error saving user '.$number.' to NS.', ['error' => $e]);
        }
    }
}
