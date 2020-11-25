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
        echo json_encode($allStudentId);
        echo json_encode($idList);
        // $allStudentsId = [];
        // foreach ($allStudents as $allStudent) {
        //     $allStudentsId[] = $allStudent->user_id;
        // }

        // foreach ($idList as $id) {
        //     $allImportantInfomationsContent = [
        //         'title' => mb_substr($allImportantInfomation->title, 0, 40),
        //         'content' => mb_substr($allImportantInfomation->content, 0, 60),
        //         'uri' => $allImportantInfomation->uri,
        //         'label' => '詳細'
        //     ];
        //     $allImportantInfomationsContents[] = $allImportantInfomationsContent;
        // }


        // $message = [
        //     "to" => $allStudentsId,
        //     "type" => "multiple",
        //     "altText" =>  "重要情報",
        //     "contents" => $allImportantInfomationsContents
        // ];

        // // post
        // $data = json_encode([$message], JSON_UNESCAPED_UNICODE);
        // error_log($data);
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
