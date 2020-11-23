<?php

namespace App\push;

use Illuminate\Support\Facades\DB;


class PushCancelInfo
{
  public function pushCancelInfo()
  {
    error_log("pushCancelINfo...");
    $allMessages = []; //最後に使う

    date_default_timezone_set('Asia/Tokyo');
    $today = date("Y-m-d");

    // CS学部
    $csStudents = DB::table('students')->select('user_id')
      ->where([
        ['push_cancel', true],
        ['department', 'cs']
      ])->get();
    $csStudentsId = [];
    foreach ($csStudents as $csStudent) {
      $csStudentsId[] = $csStudent->user_id;
    }
    $csCancelInfomationsContents = [];
    $csCancelInfomations = DB::table('cancel_informations')
      ->where([
        ['date', '>=', $today],
        ['department', 'cs']
      ])->orderBy('date', 'asc')->limit(10)->get();
    if ($csCancelInfomations->isEmpty()) {
      $message = [
        "to" => $csStudentsId,
        "type" => "text",
        "text" => "あなたの学部の休講案内はありません",
      ];
    } else {
      foreach ($csCancelInfomations as $csCancelInfomation) {
        $title = mb_substr($csCancelInfomation->date . "\n"  .
          $csCancelInfomation->period . "\n" .
          $csCancelInfomation->lecture_name, 0, 40);
        $csCancelInfomationsContent = [
          'title' => $title,
          'content' => mb_substr($csCancelInfomation->department, 0, 60),
          'uri' => 'https://service.cloud.teu.ac.jp/inside2/hachiouji/hachioji_common/cancel/',
          'label' => '詳細'
        ];
        $csCancelInfomationsContents[] = $csCancelInfomationsContent;
      }
      $message = [
        "to" => $csStudentsId,
        "type" => "multiple",
        "altText" =>  "休講案内",
        "contents" => $csCancelInfomationsContents
      ];
    }
    $allMessages[] = $message;

    // es学部
    $esStudents = DB::table('students')->select('user_id')
      ->where([
        ['push_cancel', false],
        ['department', 'es']
      ])->get();
    $esStudentsId = [];
    foreach ($esStudents as $esStudent) {
      $esStudentsId[] = $esStudent->user_id;
    }
    $esCancelInfomationsContents = [];
    $esCancelInfomations = DB::table('cancel_informations')
      ->where([
        ['date', '>=', $today],
        ['department', 'es']
      ])->orderBy('date', 'asc')->limit(10)->get();
    if ($esCancelInfomations->isEmpty()) {
      $message = [
        "to" => $esStudentsId,
        "type" => "text",
        "text" => "あなたの学部の休講案内はありません",
      ];
    } else {
      foreach ($esCancelInfomations as $esCancelInfomation) {
        $title = mb_substr($esCancelInfomation->date . "\n"  .
          $esCancelInfomation->period . "\n" .
          $esCancelInfomation->lecture_name, 0, 40);
        $esCancelInfomationsContent = [
          'title' => $title,
          'content' => mb_substr($esCancelInfomation->department, 0, 60),
          'uri' => 'https://service.cloud.teu.ac.jp/inside2/hachiouji/hachioji_common/cancel/',
          'label' => '詳細'
        ];
        $esCancelInfomationsContents[] = $esCancelInfomationsContent;
      }
      $message = [
        "to" => $esStudentsId,
        "type" => "multiple",
        "altText" =>  "休講案内",
        "contents" => $esCancelInfomationsContents
      ];
    }
    $allMessages[] = $message;

    // ms学部
    $msStudents = DB::table('students')->select('user_id')
      ->where([
        ['push_cancel', false],
        ['department', 'ms']
      ])->get();
    $msStudentsId = [];
    foreach ($msStudents as $msStudent) {
      $msStudentsId[] = $msStudent->user_id;
    }
    $msCancelInfomationsContents = [];
    $msCancelInfomations = DB::table('cancel_informations')
      ->where([
        ['date', '>=', $today],
        ['department', 'ms']
      ])->orderBy('date', 'asc')->limit(10)->get();
    if ($msCancelInfomations->isEmpty()) {
      $message = [
        "to" => $msStudentsId,
        "type" => "text",
        "text" => "あなたの学部の休講案内はありません",
      ];
    } else {
      foreach ($msCancelInfomations as $msCancelInfomation) {
        $title = mb_substr($msCancelInfomation->date . "\n"  .
          $msCancelInfomation->period . "\n" .
          $msCancelInfomation->lecture_name, 0, 40);
        $msCancelInfomationsContent = [
          'title' => $title,
          'content' => mb_substr($msCancelInfomation->department, 0, 60),
          'uri' => 'https://service.cloud.teu.ac.jp/inside2/hachiouji/hachioji_common/cancel/',
          'label' => '詳細'
        ];
        $msCancelInfomationsContents[] = $msCancelInfomationsContent;
      }
      $message = [
        "to" => $msStudentsId,
        "type" => "multiple",
        "altText" =>  "休講案内",
        "contents" => $msCancelInfomationsContents
      ];
    }
    $allMessages[] = $message;

    // BS学部
    $bsStudents = DB::table('students')->select('user_id')
      ->where([
        ['push_cancel', false],
        ['department', 'bs']
      ])->get();
    $bsStudentsId = [];
    foreach ($bsStudents as $bsStudent) {
      $bsStudentsId[] = $bsStudent->user_id;
    }
    $bsCancelInfomationsContents = [];
    $bsCancelInfomations = DB::table('cancel_informations')
      ->where([
        ['date', '>=', $today],
        ['department', 'bs']
      ])->orderBy('date', 'asc')->limit(10)->get();
    if ($bsCancelInfomations->isEmpty()) {
      $message = [
        "to" => $bsStudentsId,
        "type" => "text",
        "text" => "あなたの学部の休講案内はありません",
      ];
    } else {
      foreach ($bsCancelInfomations as $bsCancelInfomation) {
        $title = mb_substr($bsCancelInfomation->date . "\n"  .
          $bsCancelInfomation->period . "\n" .
          $bsCancelInfomation->lecture_name, 0, 40);
        $bsCancelInfomationsContent = [
          'title' => $title,
          'content' => mb_substr($bsCancelInfomation->department, 0, 60),
          'uri' => 'https://service.cloud.teu.ac.jp/inside2/hachiouji/hachioji_common/cancel/',
          'label' => '詳細'
        ];
        $bsCancelInfomationsContents[] = $bsCancelInfomationsContent;
      }
      $message = [
        "to" => $bsStudentsId,
        "type" => "multiple",
        "altText" =>  "休講案内",
        "contents" => $bsCancelInfomationsContents
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
