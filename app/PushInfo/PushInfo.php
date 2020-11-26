<?php

namespace App\PushInfo;

use Illuminate\Support\Facades\DB;

class PushInfo
{
    public function pusnImportant($idList)
    {
        // 全生徒のuser_id $allStudentsId[]
        error_log("##################### pushImportant ##################");
        $allStudentId = DB::table('students')->where('push_important', true)->pluck('user_id');
        error_log(json_encode($allStudentId));
        error_log(json_encode($idList));
        $infomations = DB::table('informations')
            ->whereIn('id', $idList)
            ->orderBy('posted_date', 'desc')->limit(10)->get();
        error_log("ここまで");
        foreach ($infomations as $infomation) {
            $title4digit = mb_substr($infomation->title, 0, 4);
            $title = $title4digit != "【重要】"  ? "【重要】" . $infomation->title : $infomation->title;
            $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
            // error_log($infomation->title);
            // error_log($infomation->uri);
            $content = [
                'title' => mb_substr($title, 0, 40),
                'content' => mb_substr($text, 0, 60),
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
        error_log(json_encode($message), JSON_UNESCAPED_UNICODE);

        // post
        $data = json_encode([$message], JSON_UNESCAPED_UNICODE);
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

    public function pusnNew($infoList)
    {
        error_log("##################### pushNew ##################");
        error_log(json_encode($infoList));
        $allMessages = [];
        // [[[all_department, cs, ms, bs], 5]]
        foreach ($infoList as $info) {
            // all_departmentが含まれる
            if (in_array('all_department', $info[0])) {
                $allStudentId = DB::table('students')->where('push_important', true)->pluck('user_id');
                $infomation = DB::table('informations')
                    ->where('id', $info[1])->get();
                $title4digit = mb_substr($infomation->title, 0, 4);
                $title = $title4digit != "【新着】"  ? "【新着】" . $infomation->title : $infomation->title;
                $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
                $content = [
                    'title' => mb_substr($title, 0, 40),
                    'content' => mb_substr($text, 0, 60),
                    'uri' => $infomation->uri,
                    'label' => '詳細'
                ];
                $message = [
                    "to" => $allStudentId,
                    "type" => "multiple",
                    "altText" =>  "新着情報",
                    "contents" => $content
                ];
            } else { // 各学部
                $allStudentId = [];
                foreach ($info[0] as $department) {
                    echo "$department" . PHP_EOL;
                    ################################
                    $studentId = DB::table('students')
                        ->where('push_new', true)->where('department', $department)
                        ->pluck('user_id');
                    $allStudentId = array_merge($allStudentId, $studentId);
                    ################################
                }
                $infomation = DB::table('informations')
                    ->where('id', $info[1])->get();
                $title4digit = mb_substr($infomation->title, 0, 4);
                $title = $title4digit != "【新着】"  ? "【新着】" . $infomation->title : $infomation->title;
                $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
                $content = [
                    'title' => mb_substr($title, 0, 40),
                    'content' => mb_substr($text, 0, 60),
                    'uri' => $infomation->uri,
                    'label' => '詳細'
                ];
                $message = [
                    "to" => $allStudentId,
                    "type" => "multiple",
                    "altText" =>  "新着情報",
                    "contents" => $content
                ];
            }
            $allMessages[] = $message;
        }
        // 要素を切る
        $allMessages = array_slice($allMessages, 0, 10);
        // post
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
