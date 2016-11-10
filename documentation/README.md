# mobilecommonsd

This is Northstar's __mobilecommonsd__, a lightweight worker which synchronizes changes in
[Mobile Commons](https://mobilecommons.com) profiles into our user API. Any changes made in
Mobile Commons should be reflected in Northstar within 5 minutes.

### Commands
Mobilecommonsd has two commands:

`mobilecommons:backfill` will fill in historical profile changes by iterating over the entire history
provided by Mobile Common's [`profiles` endpoint](https://mobilecommons.zendesk.com/hc/en-us/articles/202052534-REST-API#ListAllProfiles).

`mobilecommons:fetch` synchronizes any profiles changes from Mobile Commons to the associated Northstar
profile. It runs every 5 minutes, scheduled by the Artisan Kernel.

### Implementation
Both of the above commands rely on the [`LoadPaginatedResults`]() and [`SendUserToNorthstar`]() queued jobs.
We use Laravel's [built-in queue daemon](https://laravel.com/docs/5.3/queues#running-the-queue-worker) to process these jobs and
handle retries and failures.

All updates are sent to Northstar's [create user](https://github.com/DoSomething/northstar/blob/dev/documentation/endpoints/users.md#create-a-user) endpoint
and rely on its upsert functionality to match changes to the correct profile. This allows us to fill missing fields on existing
accounts or create new accounts without any complicated logic in this service.

