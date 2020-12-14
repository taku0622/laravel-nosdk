<?php

namespace App\PushInfo;

use Illuminate\Support\Facades\DB;

use App\Actives\Actives;


class PushInfo
{
    // public function pusnImportant($idList)
    // {
    //     // 全生徒のuser_id $allStudentsId[]
    //     error_log("##################### pushImportant ##################");
    //     $allStudentId = DB::table('students')->where('push_important', true)->pluck('user_id');
    //     error_log(json_encode($allStudentId));
    //     error_log(json_encode($idList));
    //     $infomations = DB::table('informations')
    //         ->whereIn('id', $idList)
    //         ->orderBy('posted_date', 'desc')->limit(10)->get();
    //     error_log("ここまで");
    //     foreach ($infomations as $infomation) {
    //         $title4digit = mb_substr($infomation->title, 0, 4);
    //         $title = $title4digit != "【重要】"  ? "【重要】" . $infomation->title : $infomation->title;
    //         $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
    //         // error_log($infomation->title);
    //         // error_log($infomation->uri);
    //         $content = [
    //             'title' => mb_substr($title, 0, 40),
    //             'content' => mb_substr($text, 0, 60),
    //             'uri' => $infomation->uri,
    //             'label' => '詳細'
    //         ];
    //         $contents[] = $content;
    //     }

    //     $message = [
    //         "to" => $allStudentId,
    //         "type" => "multiple",
    //         "altText" =>  "重要情報",
    //         "contents" => $contents
    //     ];
    //     error_log(json_encode($message), JSON_UNESCAPED_UNICODE);

    //     // post
    //     $data = json_encode([$message], JSON_UNESCAPED_UNICODE);
    //     error_log($data);
    //     $options = array(
    //         'http' => array(
    //             'method' => 'POST',
    //             'header' => "Content-type: text/plain\n"
    //                 . "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36\r\n" // 適当に名乗ったりできます
    //                 . "Content-Length: " . strlen($data) . "\r\n",
    //             'content' => $data
    //         )
    //     );
    //     error_log(json_encode($data, JSON_UNESCAPED_UNICODE));
    //     $context = stream_context_create($options);
    //     $response = file_get_contents('https://tut-line-bot-test.glitch.me/push', false, $context);
    // }

    // public function pusnNew($infoList)
    // {
    //     error_log("##################### pushNew ##################");
    //     $allMessages = [];
    //     $allStudents = DB::table('students')->where('push_new', true)->get();
    //     if ($allStudents->isEmpty()) {
    //         exit;
    //     }
    //     foreach ($allStudents as $student) {
    //         // [[[all_department, cs, ms, bs], 5]]
    //         error_log($student->user_id);
    //         $contents = [];
    //         foreach ($infoList as $info) {
    //             if (in_array('all_department', $info[0]) || in_array($student->department, $info[0]) || ($student->department == "all_department")) {
    //                 $infomation = DB::table('informations')->where('id', $info[1])->first();
    //                 $title4digit = mb_substr($infomation->title, 0, 4);
    //                 $title = $title4digit != "【新着】"  ? "【新着】" . $infomation->title : $infomation->title;
    //                 $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
    //                 $content = [
    //                     'title' => mb_substr($title, 0, 40),
    //                     'content' => mb_substr($text, 0, 60),
    //                     'uri' => $infomation->uri,
    //                     'label' => '詳細'
    //                 ];
    //                 $contents[] = $content;
    //             }
    //         }
    //         // pushする情報がない人
    //         if ($contents == []) {
    //             continue;
    //         }
    //         // 10個に制限
    //         $contents = array_slice($contents, 0, 10);
    //         $message = [
    //             "to" => [$student->user_id],
    //             "type" => "multiple",
    //             "altText" =>  "新着情報",
    //             "contents" => $contents
    //         ];
    //         $allMessages[] = $message;
    //     }
    //     // post
    //     $data = json_encode($allMessages, JSON_UNESCAPED_UNICODE);
    //     error_log($data);
    //     $options = array(
    //         'http' => array(
    //             'method' => 'POST',
    //             'header' => "Content-type: text/plain\n"
    //                 . "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36\r\n" // 適当に名乗ったりできます
    //                 . "Content-Length: " . strlen($data) . "\r\n",
    //             'content' => $data
    //         )
    //     );
    //     error_log(json_encode($data, JSON_UNESCAPED_UNICODE));
    //     $context = stream_context_create($options);
    //     $response = file_get_contents('https://tut-line-bot-test.glitch.me/push', false, $context);
    // }
    ###################################################################################################################
    public function pusnIinformations($idList)
    {
        $count = count($idList);
        $count = $count >= 10 ? 10 : $count;
        $allMessages = [];
        // 全生徒のuser_id $allStudentsId[]
        // [重要1,重要2,重要3]
        error_log("##################### pushImportant ##################");
        $allStudentId = DB::table('students')->where('push_important', true)
            ->where('push_new', false)->pluck('user_id');
        error_log(json_encode($allStudentId));
        error_log(json_encode($idList));
        // 分析配列
        $pushImportantUserId = $allStudentId;

        // $infomations = DB::table('informations')->join('tags', 'informations.id', '=', 'tags.information_id')
        //     ->whereIn('informations.id', $idList)->where('tags.important', true)
        //     ->orderBy('informations.posted_date', 'desc')->limit($count)->get();
        $infomations = DB::table('informations')->join('tags', 'informations.id', '=', 'tags.information_id')
            ->whereIn('informations.id', $idList)->where('tags.important', true)
            ->orderBy('informations.id', 'desc')->limit($count)->get();
        if (($infomations->isEmpty()) || ($allStudentId == [])) {
            //何もしない
            error_log("何もしない");
        } else {
            error_log("ここまで");
            foreach ($infomations as $infomation) {
                $title4digit = mb_substr($infomation->title, 0, 4);
                $title = $title4digit != "【重要】"  ? "【重要】" . $infomation->title : $infomation->title;
                $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
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
            $allMessages[] = $message;
        }

        error_log("##################### pushNew ##################");
        // [新着1,重要1,新着2,重要2,重要3,新着3]
        $allStudents = DB::table('students')->where('push_important', true)
            ->where('push_new', true)->get();
        if ($allStudents->isEmpty()) {
            // なにもしない
        } else {
            $pushNewUserId = [];
            foreach ($allStudents as $student) {
                // 分析配列
                $pushNewUserId[] = $student->user_id;

                $contents = [];
                if ($student->department == "all_department") {
                    // $infomations = DB::table('informations')->join('tags', 'informations.id', '=', 'tags.information_id')
                    //     ->whereIn('informations.id', $idList)->orderBy('posted_date', 'desc')->limit($count)->get();
                    $infomations = DB::table('informations')->join('tags', 'informations.id', '=', 'tags.information_id')
                        ->whereIn('informations.id', $idList)->orderBy('informations.id', 'desc')->limit($count)->get();
                    error_log("パターン全学部");
                } else {
                    // $infomations = DB::table('informations')->join('tags', 'informations.id', '=', 'tags.information_id')
                    //     ->whereIn('informations.id', $idList)->where('tags.all_department', true)->orWhere($student->department, true)->orderBy('posted_date', 'desc')->limit($count)->get();
                    $infomations = DB::table('informations')->join('tags', 'informations.id', '=', 'tags.information_id')
                        ->whereIn('informations.id', $idList)->where('tags.all_department', true)->orWhere($student->department, true)->orderBy('informations.id', 'desc')->limit($count)->get();
                    error_log("パターン各学部");
                    error_log($infomations->count());
                }
                if ($infomations->isEmpty()) {
                    // なにもしない
                    error_log("何もしない");
                    continue;
                }
                foreach ($infomations as $infomation) {
                    $title4digit = mb_substr($infomation->title, 0, 4);
                    $tag_important = $infomation->important;
                    if ($tag_important) {
                        $title = $title4digit != "【重要】"  ? "【重要】" . $infomation->title : $infomation->title;
                    } else {
                        $title = $title4digit != "【新着】"  ? "【新着】" . $infomation->title : $infomation->title;
                    }
                    $text = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
                    $content = [
                        'title' => mb_substr($title, 0, 40),
                        'content' => mb_substr($text, 0, 60),
                        'uri' => $infomation->uri,
                        'label' => '詳細'
                    ];
                    $contents[] = $content;
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

        //分析
        if ($pushImportantUserId != []) {
            $active = new Actives();
            json_encode($pushImportantUserId);
            $active->Actives($pushImportantUserId, "push_important_count");
        }
        if ($pushNewUserId != []) {
            $active = new Actives();
            json_encode($pushNewUserId);
            $active->Actives($pushNewUserId, "push_new_count");
        }
    }
    ############################################################################################################
    public function pushCancel($idList)
    {
        error_log("##################### pushCancel ##################");
        $allMessages = [];
        // push on の全生徒
        $allStudents = DB::table('students')->where('push_cancel', true)->get();
        if ($allStudents->isEmpty()) {
            exit;
        }
        $pushCancelUserId = [];

        foreach ($allStudents as $student) {
            // 分析配列            
            $pushCancelUserId[] = $student->user_id;
            error_log($student->department);
            $contents = [];
            $department = $student->department;
            foreach ($idList as $id) {
                error_log("id:" . $id);
                $infomation = DB::table('cancel_informations')->where('id', $id)->first();
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
                error_log("ここまで");
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
        // 分析
        error_log("pushCancelUserId: " .  $pushCancelUserId);
        if ($pushCancelUserId != []) {
            json_encode($pushCancelUserId);
            $active = new Actives();
            $active->Actives($pushCancelUserId, "push_cancel_count");
        }
    }
}
