<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if(!is_dir(storage_path('/app/backups'))) mkdir(storage_path('/app/backups'));
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".sql";
        $command = "mysqldump --user=" . config('database.connections.mysql.username') ." --password=" . config('database.connections.mysql.password') . " --host=" . config('database.connections.mysql.host') . " " . config('database.connections.mysql.database') . " > " . storage_path() . "/app/backups/" . $filename;

        $returnVar = NULL;
        $output  = NULL;

        exec($command, $output, $returnVar);
    }
}
