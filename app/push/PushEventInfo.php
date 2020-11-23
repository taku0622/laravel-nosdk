<?php

namespace App\push;

use Illuminate\Support\Facades\DB;


class PushEventInfo
{
  public function pushEventInfo()
  {
    error_log("pushEventINfo...");
    $allMessages = []; //最後に使う

    // 今日のデータのみにする？
    date_default_timezone_set('Asia/Tokyo');
    $today = date("Y-m-d");

    // 全学部
    $allStudents = DB::table('students')->select('user_id')
      ->where('push_event', false)->get();
    $allStudentsId = [];
    foreach ($allStudents as $allStudent) {
      $allStudentsId[] = $allStudent->user_id;
    }
    error_log("ここまで");

    $allEventInfomationsContents = [];
    $allEventInfomations = DB::table('event_informations')
      ->orderBy('date', 'asc')->limit(10)->get();
    if ($allEventInfomations->isEmpty()) {
      $message = [
        "to" => $allStudentsId,
        "type" => "text",
        "text" => "イベント情報はありません",
      ];
    } else {
      foreach ($allEventInfomations as $allEventInfomation) {
        $allEventInfomationsContent = [
          'title' => mb_substr($allEventInfomation->title, 0, 40),
          'content' => mb_substr($allEventInfomation->content, 0, 60),
          'uri' => $allEventInfomation->uri,
          'label' => '詳細'
        ];
        $allEventInfomationsContents[] = $allEventInfomationsContent;
      }
      $message = [
        "to" => $allStudentsId,
        "type" => "multiple",
        "altText" =>  "イベント",
        "contents" => $allEventInfomationsContents
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
