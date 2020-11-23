<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\push\PushImportantInfo; //　重要情報

class ImportantInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:pushImportant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // php artisan command:pushImportant
        $pushImportantInfo = new PushImportantInfo();
        $pushImportantInfo->pushImportantInfo();
    }
}
