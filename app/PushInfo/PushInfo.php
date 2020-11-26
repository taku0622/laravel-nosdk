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
        $allMessages = [];
        $allStudents = DB::table('students')->where('push_new', true)->get();
        if ($allStudents->isEmpty()) {
            exit;
        }
        foreach ($allStudents as $student) {
            // [[[all_department, cs, ms, bs], 5]]
            error_log($student->user_id);
            $contents = [];
            foreach ($infoList as $info) {
                if (in_array('all_department', $info[0]) || in_array($student->department, $info[0])) {
                    $infomation = DB::table('informations')->where('id', $info[1])->first();
                    $title4digit = mb_substr($infomation->title, 0, 4);
                    $title = $title4digit != "【新着】"  ? "【新着】" . $infomation->title : $infomation->title;
                    $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
                    $content = [
                        'title' => mb_substr($title, 0, 40),
                        'content' => mb_substr($text, 0, 60),
                        'uri' => $infomation->uri,
                        'label' => '詳細'
                    ];
                    $contents[] = $content;
                }
            }
            // pushする情報がない人
            if ($contents == []) {
                continue;
            }
            // 10個に制限
            $contents = array_slice($contents, 0, 10);
            $message = [
                "to" => [$student->user_id],
                "type" => "multiple",
                "altText" =>  "新着情報",
                "contents" => $contents
            ];
            $allMessages[] = $message;
        }
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

    public function pushCancel($idList)
    {
        error_log("##################### pushCancel ##################");
        $allMessages = [];
        // push on の全生徒
        $allStudents = DB::table('students')->where('push_cancel', true)->get();
        if ($allStudents->isEmpty()) {
            exit;
        }
        foreach ($allStudents as $student) {
            error_log($student->department);
            $contents = [];
            $department = $student->department;
            foreach ($idList as $id) {
                $infomation = DB::table('informations')->where('id', $id)->first();
                if (($department == $infomation->department) || ($department == "all_department")) {
                    $title = mb_substr("【休講】" . $infomation->date . "\n"  .
                        $infomation->period . "\n" .
                        $infomation->lecture_name, 0, 40);
                    error_log($title);
                    $content = [
                        'title' => $title,
                        'content' => mb_substr($infomation->department, 0, 60),
                        'uri' => 'https://service.cloud.teu.ac.jp/inside2/hachiouji/hachioji_common/cancel/',
                        'label' => '詳細'
                    ];
                    $contents[] = $content;
                }
            }
            // pushする情報がない人
            if ($contents == []) {
                continue;
            }
            // 10個に制限
            $contents = array_slice($contents, 0, 10);
            $message = [
                "to" => [$student->user_id],
                "type" => "multiple",
                "altText" =>  "休講案内",
                "contents" => $contents
            ];
            $allMessages[] = $message;
        }
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
