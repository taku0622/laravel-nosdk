<?php

namespace App\Console\Commands;

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
        $userId = [
            "U6e0f4008a090ff5b5bef0323cae3428e"
        ];
        $contents = [
            [
                'title' => '【図書館】リクエストの結果報告＜八王子キャンパス＞',
                'content' => '10月（前半）の選書の結果、以下のリクエストが採択されました。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/64555/',
                'label' => '詳細'
            ],
            [
                'title' => '2020年度第2学期（後期）放送大学特別聴講学生',
                'content' => '放送大学特別聴講学生へ',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/wp-content/uploads/2020/10/2020_dai2gakki_housoudaigaku_1022.pdf',
                'label' => '詳細'
            ],
            [
                'title' => '【CS学部】2020年度「創成課題」教室（10/22更新）',
                'content' => '属された研究室ごとに、創成課題を行います。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/wp-content/uploads/2020/10/2020CS_souseikadai_kyousitu20201022.pdf',
                'label' => '詳細'
            ],
            [
                'title' => 'シェアサイクル設置のお知らせ（八王子キャンパス）',
                'content' => '八王子キャンパスにシェアサイクルを設置することになりました。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/wp-content/uploads/2020/10/shearingu_settiosirase_1021.pdf',
                'label' => '詳細'
            ],
            [
                'title' => '【図書館】図書館アルバイトを募集します！＜八王子キャンパス＞',
                'content' => 'お申し込みを お待ちしています。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/12658/',
                'label' => '詳細'
            ]
        ];
        $message = [
            "to" => [$userId],
            "type" => "multiple",
            "altText" =>  "新着情報",
            "contents" => $contents
        ];
        $data = json_encode($message, JSON_UNESCAPED_UNICODE);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: text/plain\n"
                    . "User-Agent: php.file_get_contents\r\n" // 適当に名乗ったりできます
                    . "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents('https://tut-line-bot-test.glitch.me/push', false, $context);
        echo gettype($response);
        echo $response;

        // return 0;
    }
}
