<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class Watson
{
  public function watson($userId, $text)
  {
    $data = array('input' => array("text" => $text));
    // 前回までの会話のデータがデータベースに保存されていれば
    if ($this->getLastConversationData($userId)) {
      $lastConversationData = $this->getLastConversationData($userId);

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
    $outputText2 = explode('\n', $outputText);
    error_log(json_encode($outputText2, JSON_UNESCAPED_UNICODE));
    return $outputText;
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
