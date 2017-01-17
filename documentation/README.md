# mobilecommonsd

This is Northstar's __mobilecommonsd__, a lightweight worker which synchronizes changes in
[Mobile Commons](https://mobilecommons.com) profiles into our user API. Any changes made in
Mobile Commons should be reflected in Northstar within 5 minutes.

### Commands
Mobilecommonsd has two commands:

`mobilecommons:backfill` will create jobs to fill in historical profile data by iterating over the entire history
provided by Mobile Common's [`profiles` endpoint](https://mobilecommons.zendesk.com/hc/en-us/articles/202052534-REST-API#ListAllProfiles).
This command **only needs to be run once** in order to "kick off" the process. After that, the queue listeners will
continue working until they're done!

`mobilecommons:fetch` synchronizes any recent profiles changes from Mobile Commons to the associated Northstar
profile. It should run automatically every 5 minutes, [scheduled by the Artisan Kernel](https://laravel.com/docs/5.3/scheduling).

`mobilecommons:status` displays a status report for Mobile Commons fetch jobs. 

### Implementation
Both of the above commands rely on the [`LoadResultsFromMobileCommons`](https://github.com/DoSomething/northstar-mobilecommonsd/blob/dev/app/Jobs/LoadResultsFromMobileCommons.php)
and [`SendUserToNorthstar`](https://github.com/DoSomething/northstar-mobilecommonsd/blob/dev/app/Jobs/SendUserToNorthstar.php) queued jobs.

All updates are sent to Northstar's [create user](https://github.com/DoSomething/northstar/blob/dev/documentation/endpoints/users.md#create-a-user) endpoint
and rely on its upsert functionality to match changes to the correct profile. This allows us to fill missing fields on existing
accounts or create new accounts without any complicated logic in this service.

### Queue Listeners
We use Laravel's [built-in queue daemon](https://laravel.com/docs/5.3/queues#running-the-queue-worker) to process these jobs and
handle retries and failures. This makes it easy for us to scale to meet demand (by increasing the number
of processes assigned to a particular queue) and easily handle retries and tracking failures.

It probably makes sense to have 1 `mobilecommons` queue worker & around 5 `northstar` queue workers.

To start a worker that will fetch user data from Mobile Commons:
 
```bash
nohup php artisan queue:work --daemon --queue=mobilecommons &
```

To start a worker that will send any fetched users to Northstar:
```bash
nohup php artisan queue:work listen --daemon --queue=northstar &
```

To view the backgrounded queue workers:

```sh
ps aux | grep "php artisan"
```

To monitor the number of jobs completed per second (credit to Sena):

```sh
TZ='GMT' watch -n 1 "grep \$(date +%T | cut -d' ' -f1) nohup.out | wc -l"
```

### In case of emergency…
If a task could not be completed successfully (for example, if Mobile Commons or Northstar timeout or return an error),
that task will be put back in the queue for later. By default, Lumen will make 5 attempts per task before giving up. 

Failed tasks can be viewed with Laravel's `php artisan queue:failed` command, and retried with `php artisan queue:retry`.
