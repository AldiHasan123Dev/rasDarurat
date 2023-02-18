<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SyncDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi Database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $export = $this->export();
        if($export){
            Artisan::call('db:wipe');
            exec('C:\xampp\mysql\bin\mysql -u '.env('DB_USERNAME').' '.env('DB_DATABASE').' < C:\xampp\htdocs\kondoku_desktop\public\database\export.sql');
        }
        return Command::SUCCESS;
    }

    public function export()
    {
        $path = public_path('database/export.sql');
        try {
            exec('C:\xampp\mysql\bin\mysqldump -u root --ignore-table=kondoku.comments --ignore-table=kondoku.post_tags --ignore-table=kondoku.posts kondoku > '.$path.'
            C:\xampp\mysql\bin\mysqldump -u root --no-data kondoku >> '.$path);
            // exec('C:\xampp\mysql\bin\mysqldump -u root -p kondoku > '.$path);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
}
