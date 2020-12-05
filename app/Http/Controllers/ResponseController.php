<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Services\Watson;

class ResponseController extends Controller
{
    public function response(Request $request)
    {
        $events = json_decode($request->getContent(), true);
        // error_log("request is" . $request->getContent());
        foreach ($events as $event) {
            error_log("################################## event is ##################################");
            error_log(json_encode($event, JSON_UNESCAPED_UNICODE));
            // $usersId = $event["to"]; // array
            $userId = $event["to"][0]; // string
            $type = $event["type"];
            if ($type == "follow") {
                $message = $this->followEvent($userId);
                error_log($message);
                return $message;
            }
            $text = $event["text"];

            error_log("userId: " . $userId . "  type: " . $type . "  text: " . $text);

            // 送信のデータの作成
            switch ($text) {
                case "新着情報":
                    $message = $this->newInfo($userId, $text);
                    break;
                case "重要情報":
                    $message = $this->importantInfo($userId, $text);
                    break;
                case "休講案内":
                    $message = $this->cancelInfo($userId, $text);
                    break;
                case "イベント":
                    $message = $this->eventInfo($userId, $text);
                    break;
                default:
                    $watson = new Watson();
                    $message = $watson->watson($userId, $text);
                    break;
            }
            $message = array($message);
            error_log(json_encode($message, JSON_UNESCAPED_UNICODE));
            error_log(gettype(json_encode($message, JSON_UNESCAPED_UNICODE)));
            // return json_encode($message, JSON_UNESCAPED_UNICODE);
            // echo json_encode($message, JSON_UNESCAPED_UNICODE);
        }
    }

    public function newInfo($userId, $text): array
    {
        // userIDから学部の特定
        $student = DB::table('students')->where('user_id', $userId)->first();
        $department = $student->department;
        // 学部を設定していなかったら全表示
        if ($student->department == 'all_department') {
            $infomations = DB::table('informations')
                ->join('tags', 'informations.id', '=', 'tags.information_id')
                ->whereNull('important')
                ->orderBy('posted_date', 'desc')->limit(10)->get();
        } else {
            $infomations = DB::table('informations')
                ->join('tags', 'informations.id', '=', 'tags.information_id')->whereNull('important')
                ->orWhere('tags.all_department', true)->where($department, true)
                ->orderBy('posted_date', 'desc')->limit(10)->get();
        }
        if ($infomations->isEmpty()) {
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => "あなたの学部の新着情報はありません",
            ];
        } else {
            $contents = [];
            foreach ($infomations as $infomation) {
                $content = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
                $content = [
                    'title' => mb_substr($infomation->title, 0, 40),
                    'content' => mb_substr($content, 0, 60),
                    'uri' => $infomation->uri,
                    'label' => '詳細'
                ];
                $contents[] = $content;
            }
            $message = [
                "to" => [$userId],
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }
    public function importantInfo($userId, $text): array
    {
        $infomations = DB::table('informations')->distinct()
            ->join('tags', 'informations.id', '=', 'tags.information_id')
            ->where('important', true)
            ->orderBy('posted_date', 'desc')->limit(10)->get();
        // error_log(json_decode($infomations, true));
        if ($infomations->isEmpty()) {
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => "あなたの学部の重要情報はありません",
            ];
        } else {
            $contents = [];
            foreach ($infomations as $infomation) {
                $content = $infomation->content == ''  ? '「詳細」を押してご確認ください。' : $infomation->content;
                $content = [
                    'title' => mb_substr($infomation->title, 0, 40),
                    'content' => mb_substr($content, 0, 60),
                    'uri' => $infomation->uri,
                    'label' => '詳細'
                ];
                $contents[] = $content;
            }
            $message = [
                "to" => [$userId],
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }

    public function cancelInfo($userId, $text): array
    {
        date_default_timezone_set('Asia/Tokyo');
        $today = date("Y-m-d");
        // userIDから学部の特定
        $student = DB::table('students')->where('user_id', $userId)->first();
        error_log($student->department);
        // 学部を設定していなかったら全表示
        if ($student->department == 'all_department') {
            $cancelInfomations = DB::table('cancel_informations')
                ->where('date', '>=', $today)
                ->orderBy('date', 'asc')->limit(10)->get();
        } else {
            $cancelInfomations = DB::table('cancel_informations')
                ->where([
                    ['department', $student->department],
                    ['date', '>=', $today]
                ])
                ->orderBy('date', 'asc')->limit(10)->get();
        }
        if ($cancelInfomations->isEmpty()) {
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => "あなたの学部の休講案内はありません",
            ];
        } else {
            $contents = [];
            foreach ($cancelInfomations as $cancelInfomation) {
                $title = mb_substr($cancelInfomation->date . "\n"  .
                    $cancelInfomation->period . "\n" .
                    $cancelInfomation->lecture_name, 0, 40);
                $content = [
                    'title' => $title,
                    'content' => mb_substr($cancelInfomation->department, 0, 60),
                    'uri' => 'https://service.cloud.teu.ac.jp/inside2/hachiouji/hachioji_common/cancel/',
                    'label' => '詳細'
                ];
                $contents[] = $content;
            }
            $message = [
                "to" => [$userId],
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }

    public function followEvent($userId)
    {
        // すでにあるか
        $student = DB::table('students')->where('user_id', $userId)->first();
        error_log(json_encode($student));
        error_log(isset($student->user_id));
        // データがない場合
        if (!isset($student->user_id)) {
            DB::table('students')->insert(
                [
                    'user_id' => $userId,
                    'number' => "",
                    'department' => "all_department",
                    'push_new' => TRUE,
                    'push_important' => TRUE,
                    'push_cancel' => TRUE,
                    'push_event' => TRUE,
                ]
            );
        }
        $message = "followed";
        return $message;
    }

    public function eventInfo($userId, $text): array
    {
        $eventInfomations = DB::table('event_informations')
            ->orderBy('posted_date', 'desc')->limit(10)->get();
        if ($eventInfomations->isEmpty()) {
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => "イベントはありません",
            ];
        } else {
            $contents = [];
            foreach ($eventInfomations as $eventInfomation) {
                $content = $eventInfomation->content == ''  ? '「詳細」を押してご確認ください。' : $eventInfomation->content;
                $content = [
                    'title' => mb_substr($eventInfomation->title, 0, 40),
                    'content' => mb_substr($eventInfomation->content, 0, 60),
                    'uri' => $eventInfomation->uri,
                    'label' => '詳細'
                ];
                $contents[] = $content;
            }
            $message = [
                "to" => [$userId],
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }
}
