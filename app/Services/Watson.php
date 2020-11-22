<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class Watson
{
  public function watson($userId, $text): array
  {
    $data = array('input' => array("text" => $text));
    // 前回までの会話のデータがデータベースに保存されていれば
    if ($this->getLastConversationData($userId)) {
      $lastConversationData = $this->getLastConversationData($userId);
      ################################################################################
      // 参考書の時
      error_log($lastConversationData["userid"]);
      error_log($lastConversationData["conversation_id"]);
      error_log($lastConversationData["dialog_node"]);
      ################################################################################
      // 前回までの会話のデータをパラメータに追加
      $data["context"] = array(
        "conversation_id" => $lastConversationData["conversation_id"],
        "system" => array(
          "dialog_stack" => array(array("dialog_node" => $lastConversationData["dialog_node"])),
          "dialog_turn_counter" => 1,
          "dialog_request_counter" => 1
        )
      );
    }
    ## ここまでが前回データ
    $data_json = json_encode($data, JSON_UNESCAPED_UNICODE);
    $headers = ['Content-Type' => 'application/json', 'Content-Length' => strlen($data_json)];
    $curlOpts = [
      CURLOPT_USERPWD        => 'apikey:' . getenv('WATSON_API_KEY'),
      CURLOPT_POSTFIELDS     => $data_json,
    ];
    $client = new Client(['base_uri' => 'https://api.us-south.assistant.watson.cloud.ibm.com/v1/workspaces/']);
    $path = getenv('WATSON_SKILL_ID') . '/message?version=2020-10-16';
    $response = $client->request('POST', $path, ['headers' => $headers, 'curl' => $curlOpts])->getBody()->getContents();
    // error_log(json_encode($response, JSON_UNESCAPED_UNICODE));
    $json = json_decode($response, true);
    $conversationId = $json["context"]["conversation_id"];
    $dialogNode = $json["context"]["system"]["dialog_stack"][0]["dialog_node"];
    // error_log($conversationId);
    // error_log($dialogNode);

    // データベースに保存
    $conversationData = array('conversation_id' => $conversationId, 'dialog_node' => $dialogNode);
    $this->setLastConversationData($userId, $conversationData);

    // Conversationからの返答を取得
    $outputText = $json['output']['text'][count($json['output']['text']) - 1];
    // 返答の文字列を配列に
    $outputArray = explode("\n", $outputText);
    // quickReplyにするか決める
    if (count($outputArray) > 2) { //要素が2個以上の時クイックリプライとする
      $quickReply = array_slice($outputArray, 1);
      $replyArray = [$outputArray[0], $quickReply];
    } else {
      $quickReply = NULL;
      $replyArray = [$outputText, $quickReply];
    }
    $message = [
      "to" => [$userId],
      "type" => "text",
      "text" => $replyArray[0],
      "quickReply" => [
        "texts" => $replyArray[1]
      ]
    ];
    // error_log(json_encode($replyArray, JSON_UNESCAPED_UNICODE));
    return $message;
  }
  // データベースから会話データを取得
  public function getLastConversationData($userId)
  {
    $data = DB::table('conversations')->where('userid', $userId)->get()->first();
    if (!$data) {
      return NULL;
    } else {
      return array('conversation_id' => $data->conversation_id, 'dialog_node' => $data->dialog_node);
    }
  }
  // 会話データをデータベースに保存
  public function setLastConversationData($userId, $lastConversationData)
  {
    $conversationId = $lastConversationData['conversation_id'];
    $dialogNode = $lastConversationData['dialog_node'];

    if (!($this->getLastConversationData($userId))) {
      DB::table('conversations')->insert([
        'conversation_id' => $conversationId,
        'dialog_node' => $dialogNode,
        'userid' => $userId
      ]);
    } else {
      DB::table('conversations')->where('userid', $userId)
        ->update([
          'conversation_id' => $conversationId,
          'dialog_node' => $dialogNode,
        ]);
    }
  }
}
