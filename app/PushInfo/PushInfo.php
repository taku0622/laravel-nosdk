<?php

namespace App\PushInfo;

use Illuminate\Support\Facades\DB;

class PushInfo
{
    public function pusnImportant($idList)
    {
        // 全生徒のuser_id $allStudentsId[]
        error_log("##################### pushInfo ##################");
        $allStudentId = DB::table('students')->where('push_important', true)->pluck('user_id');
        error_log(json_encode($allStudentId));
        error_log(json_encode($idList));
        $infomations = DB::table('informations')
            ->whereIn('id', $idList)
            ->orderBy('posted_date', 'desc')->limit(10)->get();
        foreach ($infomations as $infomation) {
            $content = [
                'title' => mb_substr($infomation->title, 0, 40),
                'content' => mb_substr($infomation->content, 0, 60),
                'uri' => $infomation->uri,
                'label' => '詳細'
            ];
            $contents[] = $content;
        }


        $message = [
            "to" => $allStudentId,
            "type" => "multiple",
            "altText" =>  "重要情報",
            "contents" => $contents
        ];
        error_log(json_encode($message));

        // post
        $data = json_encode([$message], JSON_UNESCAPED_UNICODE);
        error_log($data);
        // $options = array(
        //     'http' => array(
        //         'method' => 'POST',
        //         'header' => "Content-type: text/plain\n"
        //             . "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36\r\n" // 適当に名乗ったりできます
        //             . "Content-Length: " . strlen($data) . "\r\n",
        //         'content' => $data
        //     )
        // );
        // error_log(json_encode($data, JSON_UNESCAPED_UNICODE));
        // $context = stream_context_create($options);
        // $response = file_get_contents('https://tut-line-bot-test.glitch.me/push', false, $context);
    }
}
