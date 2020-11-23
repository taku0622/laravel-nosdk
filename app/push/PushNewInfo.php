<?php

namespace App\push;

use Illuminate\Support\Facades\DB;


class PushNewInfo
{
  public function pushNewInfo()
  {
    error_log("pushNewINfo...");
    $allMessages = []; //最後に使う

    // 今日のデータのみにする？
    date_default_timezone_set('Asia/Tokyo');
    $today = date("Y-m-d");

    // 全学部の生徒
    $allStudents = DB::table('students')->select('user_id')
      ->where([
        ['push_new', false],
        ['department', 'all_department']
      ])->get();
    $allStudentsId = [];
    foreach ($allStudents as $allStudent) {
      $allStudentsId[] = $allStudent->user_id;
    }
    // all_deartmentは全部
    $allNewInfomationsContents = [];
    $allNewInfomations = DB::table('informations')
      ->join('tags', 'informations.id', '=', 'tags.information_id')
      ->whereNull('important')
      ->orderBy('posted_date', 'desc')->limit(10)->get();
    if ($allNewInfomations->isEmpty()) {
      $message = [
        "to" => $allStudentsId,
        "type" => "text",
        "text" => "新着情報はありません",
      ];
    } else {
      foreach ($allNewInfomations as $allNewInfomation) {
        $allNewInfomationsContent = [
          'title' => mb_substr($allNewInfomation->title, 0, 40),
          'content' => mb_substr($allNewInfomation->content, 0, 60),
          'uri' => $allNewInfomation->uri,
          'label' => '詳細'
        ];
        $allNewInfomationsContents[] = $allNewInfomationsContent;
      }
      $message = [
        "to" => $allStudentsId,
        "type" => "multiple",
        "altText" =>  "新着情報",
        "contents" => $allNewInfomationsContents
      ];
    }
    $allMessages[] = $message;
    // // CS学部の生徒
    // $allStudents = DB::table('students')->select('user_id')
    //   ->where([
    //     ['push_new', false],
    //     ['department', 'cs']
    //   ])->get();
    // $allStudentsId = [];
    // foreach ($allStudents as $allStudent) {
    //   $allStudentsId[] = $allStudent->user_id;
    // }
    // // cs
    // $allImportantInfomationsContents = [];
    // $allImportantInfomations = DB::table('informations')
    //   ->join('tags', 'informations.id', '=', 'tags.information_id')
    //   ->where('cs', true)
    //   ->whereNull('important')
    //   ->orderBy('posted_date', 'desc')->limit(10)->get();
    // if ($allImportantInfomations->isEmpty()) {
    //   $message = [
    //     "to" => $allStudentsId,
    //     "type" => "text",
    //     "text" => "新着情報はありません",
    //   ];
    // } else {
    //   foreach ($allImportantInfomations as $allImportantInfomation) {
    //     $allImportantInfomationsContent = [
    //       'title' => mb_substr($allImportantInfomation->title, 0, 40),
    //       'content' => mb_substr($allImportantInfomation->content, 0, 60),
    //       'uri' => $allImportantInfomation->uri,
    //       'label' => '詳細'
    //     ];
    //     $allImportantInfomationsContents[] = $allImportantInfomationsContent;
    //   }
    //   $message = [
    //     "to" => $allStudentsId,
    //     "type" => "multiple",
    //     "altText" =>  "新着情報",
    //     "contents" => $allImportantInfomationsContents
    //   ];
    // }

    // // 全学部の生徒
    // $allStudents = DB::table('students')->select('user_id')
    //   ->where([
    //     ['push_new', false],
    //     ['department', 'all_department']
    //   ])->get();
    // $allStudentsId = [];
    // foreach ($allStudents as $allStudent) {
    //   $allStudentsId[] = $allStudent->user_id;
    // }
    // // all_deartmentは全部
    // $allImportantInfomationsContents = [];
    // $allImportantInfomations = DB::table('informations')
    //   ->join('tags', 'informations.id', '=', 'tags.information_id')
    //   ->whereNull('important')
    //   ->orderBy('posted_date', 'desc')->limit(10)->get();
    // if ($allImportantInfomations->isEmpty()) {
    //   $message = [
    //     "to" => $allStudentsId,
    //     "type" => "text",
    //     "text" => "新着情報はありません",
    //   ];
    // } else {
    //   foreach ($allImportantInfomations as $allImportantInfomation) {
    //     $allImportantInfomationsContent = [
    //       'title' => mb_substr($allImportantInfomation->title, 0, 40),
    //       'content' => mb_substr($allImportantInfomation->content, 0, 60),
    //       'uri' => $allImportantInfomation->uri,
    //       'label' => '詳細'
    //     ];
    //     $allImportantInfomationsContents[] = $allImportantInfomationsContent;
    //   }
    //   $message = [
    //     "to" => $allStudentsId,
    //     "type" => "multiple",
    //     "altText" =>  "新着情報",
    //     "contents" => $allImportantInfomationsContents
    //   ];
    // }
    // $allMessages[] = $message;

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
