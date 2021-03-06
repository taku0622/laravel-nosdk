<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Services\Watson;
use App\Actives\Actives;

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
            $replyToken = $event["replyToken"];

            error_log("userId: " . $userId . "  type: " . $type . "  text: " . $text . "  replyToken: " . $replyToken);
            // 分析
            $active = new Actives();
            $active->Actives([$userId], $text);

            // 送信のデータの作成
            switch ($text) {
                case "新着情報":
                    $message = $this->newInfo($userId, $text, $replyToken);
                    break;
                case "重要情報":
                    $message = $this->importantInfo($userId, $text, $replyToken);
                    break;
                case "休講案内":
                    $message = $this->cancelInfo($userId, $text, $replyToken);
                    break;
                case "イベント":
                    $message = $this->eventInfo($userId, $text, $replyToken);
                    break;
                default:
                    $watson = new Watson();
                    $message = $watson->watson($userId, $text, $replyToken);
                    break;
            }
            $message = array($message);
            error_log(json_encode($message, JSON_UNESCAPED_UNICODE));
            error_log(gettype(json_encode($message, JSON_UNESCAPED_UNICODE)));
            return json_encode($message, JSON_UNESCAPED_UNICODE);
            // echo json_encode($message, JSON_UNESCAPED_UNICODE);
        }
    }

    public function newInfo($userId, $text, $replyToken): array
    {
        // userIDから学部の特定
        $student = DB::table('students')->where('user_id', $userId)->first();
        $department = $student->department;
        // 学部を設定していなかったら全表示
        if ($student->department == 'all_department') {
            // $uriList = DB::table('informations')->select('informations.uri')
            //     ->join('tags', 'informations.id', '=', 'tags.information_id')
            //     ->whereNull('important')->groupBy('informations.uri')
            //     ->orderByRaw('max(informations.posted_date) desc')->limit(10)->get();
            $uriList = DB::table('informations')->select('informations.uri')
                ->join('tags', 'informations.id', '=', 'tags.information_id')
                ->whereNull('important')->groupBy('informations.uri')
                ->orderByRaw('max(informations.id) desc')->limit(10)->get();
        } else {
            // $uriList = DB::table('informations')->select('informations.uri')
            //     ->join('tags', 'informations.id', '=', 'tags.information_id')->whereNull('important')
            //     ->orWhere('tags.all_department', true)->where($department, true)
            //     ->groupBy('informations.uri')
            //     ->orderByRaw('max(informations.posted_date) desc')->limit(10)->get();
            $uriList = DB::table('informations')->select('informations.uri')
                ->join('tags', 'informations.id', '=', 'tags.information_id')->whereNull('important')
                ->orWhere('tags.all_department', true)->where($department, true)
                ->groupBy('informations.uri')
                ->orderByRaw('max(informations.id) desc')->limit(10)->get();
        }
        if ($uriList->isEmpty()) {
            $message = [
                "to" => [$userId],
                "replyToken" => $replyToken,
                "type" => "text",
                "text" => "あなたの学部の新着情報はありません",
            ];
        } else {
            $contents = [];
            foreach ($uriList as $uri) {
                $information = DB::table('informations')->where('uri', $uri->uri)->orderBy('posted_date', 'desc')->first();
                $content = $information->content == ''  ? '「詳細」を押してご確認ください。' : $information->content;
                $content = [
                    'title' => mb_substr($information->title, 0, 40),
                    'content' => mb_substr($content, 0, 60),
                    'uri' => $information->uri,
                    'label' => '詳細'
                ];
                $contents[] = $content;
            }
            $message = [
                "to" => [$userId],
                "replyToken" => $replyToken,
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }
    public function importantInfo($userId, $text, $replyToken): array
    { // uriで重複を消す(※更新など)更新されたデータだけを抽出
        // $uriList = DB::table('informations')->select('informations.uri')
        //     ->join('tags', 'informations.id', '=', 'tags.information_id')
        //     ->where('important', true)->groupBy('informations.uri')
        //     ->orderByRaw('max(informations.posted_date) desc')->limit(10)->get();
        $uriList = DB::table('informations')->select('informations.uri')
            ->join('tags', 'informations.id', '=', 'tags.information_id')
            ->where('important', true)->groupBy('informations.uri')
            ->orderByRaw('max(informations.id) desc')->limit(10)->get();
        if ($uriList->isEmpty()) {
            $message = [
                "to" => [$userId],
                "replyToken" => $replyToken,
                "type" => "text",
                "text" => "あなたの学部の重要情報はありません",
            ];
        } else {
            $contents = [];
            foreach ($uriList as $uri) {
                #########################################
                // error_log($uri->uri);
                $information = DB::table('informations')->where('uri', $uri->uri)->orderBy('posted_date', 'desc')->first();
                // error_log($information->title);
                #########################################
                $content = $information->content == ''  ? '「詳細」を押してご確認ください。' : $information->content;
                $content = [
                    'title' => mb_substr($information->title, 0, 40),
                    'content' => mb_substr($content, 0, 60),
                    'uri' => $information->uri,
                    'label' => '詳細'
                ];
                $contents[] = $content;
            }
            $message = [
                "to" => [$userId],
                "replyToken" => $replyToken,
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }

    public function cancelInfo($userId, $text, $replyToken): array
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
                "replyToken" => $replyToken,
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
                "replyToken" => $replyToken,
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }

    public function followEvent($userId)
    {
        // データがない場合
        date_default_timezone_set('Asia/Tokyo');
        if (DB::table('students')->where('user_id', $userId)->doesntExist()) {
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
            error_log("ここまで");
            DB::table('actives')->insert(
                [
                    'user_id' => $userId,
                    'question_count' => 0,
                    'important_count' => 0,
                    'new_count' => 0,
                    'canel_count' => 0,
                    'event_count' => 0,
                    'setting_count' => 0,
                    'other_count' => 0,
                    'push_important_count' => 0,
                    'push_new_count' => 0,
                    'push_cancel_count' => 0,
                    'push_event_count' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
        }
        $message = "followed";
        return $message;
    }

    public function eventInfo($userId, $text, $replyToken): array
    {
        $eventInfomations = DB::table('event_informations')
            ->orderBy('posted_date', 'desc')->limit(10)->get();
        if ($eventInfomations->isEmpty()) {
            $message = [
                "to" => [$userId],
                "replyToken" => $replyToken,
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
                "replyToken" => $replyToken,
                "type" => "multiple",
                "altText" =>  $text,
                "contents" => $contents
            ];
        }
        return $message;
    }
}
