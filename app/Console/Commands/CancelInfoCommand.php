<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\push\PushCancelInfo; // 休講

class CancelInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:pushCancel';

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
        // php artisan command:pushCancel
        $pushCancelInfo = new PushCancelInfo();
        $pushCancelInfo->pushCancelInfo();
    }
}
