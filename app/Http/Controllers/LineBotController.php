<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Watson;

class LineBotController extends Controller
{
    public function index()
    {
        return view('linebot.index');
    }
    public function parrot(Request $request)
    {
        error_log("hello...");

        $input = $request->getContent();
        if (!empty($input)) {
            $events = json_decode($input)->events;
            foreach ($events as $event) {
                // error_log(json_encode($event, JSON_UNESCAPED_UNICODE));
                $this->bot($event);
            }
        }
    }
    public function bot($event)
    {
        // ユーザー入力を取得
        $text = $event->message->text;
        $userId = $event->source->userId;
        $replytoken = $event->replyToken;
        error_log("text :" . $text);
        error_log("userid :" . $userId);
        error_log("replytoken :" . $replytoken);
        $this->reply($userId, $replytoken, $text);
    }
    function reply($userId, $replytoken, $text)
    {
        $watson = new Watson();
        $Response = $watson->watson($userId, $text);
        // error_log($Response);
        $messages =
            [
                "type" => "text",
                "text" =>  $Response
            ];
        $object = [
            'replyToken' => $replytoken,
            'messages' => [
                $messages
            ]
        ];
        $this->post($object);
    }

    // LINEサーバへ送信実行関数
    function post($object)
    {
        // JSON形式への変換
        $json =  json_encode($object, JSON_UNESCAPED_UNICODE);
        error_log(json_encode($object, JSON_UNESCAPED_UNICODE));
        //curl実行
        $ch = curl_init("https://api.line.me/v2/bot/message/reply");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charser=UTF-8',
            'Authorization: Bearer ' . getenv('LINE_ACCESS_TOKEN')
        ));
        $result = curl_exec($ch);
        curl_close($ch);
    }
}
