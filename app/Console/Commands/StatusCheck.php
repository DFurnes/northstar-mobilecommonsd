<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;

class StatusCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mobilecommons:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display status of MobileCommons imports by time frame.';

    /**
     * Execute the console command.
     *
     * @param DatabaseManager|Connection $db
     */
    public function handle(DatabaseManager $db)
    {
        // Return results as an array for placing into table.
        $db->setFetchMode(\PDO::FETCH_ASSOC);

        $status = $db->select('SELECT * FROM progress;');

        // Format the table for readability.
        $status = collect($status)->sortBy('start')->map(function ($row) {
            unset($row['id']);
            $row['start'] = Carbon::parse($row['start'])->format('M d, Y');
            $row['end'] = Carbon::parse($row['end'])->format('M d, Y');
            $row['done'] = $row['done'] === 1 ? '✔' : '';

            return $row;
        });

        $this->table(['Start', 'End', 'Pages', 'Finished'], $status);
    }
}
