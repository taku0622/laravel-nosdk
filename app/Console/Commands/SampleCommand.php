<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class SampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sample';

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
        // php artisan command:sample
        error_log("pushImportantINfo...");
        $allMessages = []; //最後に使う

        // 今日のデータのみにする？
        date_default_timezone_set('Asia/Tokyo');
        $today = date("Y-m-d");

        // 全学部
        $allStudents = DB::table('students')->select('user_id')
            ->where('push_important', true)->get();
        $allStudentsId = [];
        foreach ($allStudents as $allStudent) {
            $allStudentsId[] = $allStudent->user_id;
        }

        $allImportantInfomationsContents = [];
        $allImportantInfomations = DB::table('informations')
            ->join('tags', 'informations.id', '=', 'tags.information_id')
            ->where('important', true)
            ->orderBy('posted_date', 'desc')->limit(10)->get();
        if ($allImportantInfomations->isEmpty()) {
            $message = [
                "to" => $allStudentsId,
                "type" => "text",
                "text" => "重要情報はありません",
            ];
        } else {
            foreach ($allImportantInfomations as $allImportantInfomation) {
                $allImportantInfomationsContent = [
                    'title' => mb_substr($allImportantInfomation->title, 0, 40),
                    'content' => mb_substr($allImportantInfomation->content, 0, 60),
                    'uri' => $allImportantInfomation->uri,
                    'label' => '詳細'
                ];
                $allImportantInfomationsContents[] = $allImportantInfomationsContent;
            }
            $message = [
                "to" => $allStudentsId,
                "type" => "multiple",
                "altText" =>  "重要情報",
                "contents" => $allImportantInfomationsContents
            ];
        }
        $allMessages[] = $message;

        $data = json_encode($allMessages, JSON_UNESCAPED_UNICODE);
        error_log($data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: text/plain\n"
                    . "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36\r\n" // 適当に名乗ったりできます
                    . "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data
            )
        );
        error_log(json_encode($data, JSON_UNESCAPED_UNICODE));
        $context = stream_context_create($options);
        $response = file_get_contents('https://tut-line-bot-test.glitch.me/push', false, $context);
    }
}
