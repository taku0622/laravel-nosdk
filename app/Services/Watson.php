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
            #######################################################################
            error_log($lastConversationData["conversation_id"]);
            error_log($lastConversationData["dialog_node"]);
            $conversation_id = $lastConversationData["conversation_id"];
            $dialog_node = $lastConversationData["dialog_node"];
            // 講義確定orあいまい
            if ($dialog_node == "node_1_1606031433273") {
                $message = $this->serchReference1($userId, $text, $conversation_id);
                return $message;
            }
            // 講師確定
            if (mb_substr($dialog_node, 0, 21) == "node_10_1606035689190") {
                $lecture_name = mb_substr($dialog_node, 21);
                error_log($text); //講師
                error_log($lecture_name); //講義名
                $message = $this->serchReference2($userId, $text, $conversation_id, $lecture_name);
                return $message;
            }
            #######################################################################
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


    // 参考書サーチ1
    public function serchReference1($userId, $text, $conversation_id)
    {
        // $textで参考書検索
        $referenceInfomations = DB::table('reference_informations')
            ->where('lecture_name', 'LIKE', '%' . $text . '%')->get();
        $referenceInfomations2 = DB::table('reference_informations')
            ->where('lecture_name', 'LIKE', $text)->get();
        error_log(count($referenceInfomations));
        $count = count($referenceInfomations);
        error_log(count($referenceInfomations2));
        $count2 = count($referenceInfomations2);
        if (($count == $count2) && ($count2 != 1)) { // 一つあるだけ["創成課題","創成課題","創成課題","創成課題","創成課題","創成課題","創成課題","創成課題","創成課題","創成課題","創成課題","創成課題","創成課題"]
            $dialog_node = 'node_10_1606035689190' . $text;
            // 会話dbに保存
            DB::table('conversations')->where('userid', $userId)
                ->update([
                    'conversation_id' => $conversation_id,
                    'dialog_node' => $dialog_node,
                ]);
            // メッセージ生成
            $names = [];
            foreach ($referenceInfomations as $referenceInfomation) {
                $names[] = $referenceInfomation->teacher_name;
            }
            // names配列切り取り限度13(line quick reply)
            $names13 = array_slice($names, 0, 13);
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => $count . "件見つかりました。\n講師の名前を入力してください。\nクイックリプライになければ入力してください",
                "quickReply" => [
                    "texts" => $names13
                ]
            ];
            return $message;
        }
        // ない  
        if ($referenceInfomations->isEmpty()) {
            $dialog_node = 'root';
            // 会話dbに保存
            DB::table('conversations')->where('userid', $userId)
                ->update([
                    'conversation_id' => $conversation_id,
                    'dialog_node' => $dialog_node,
                ]);
            // メッセージ生成
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => "講義が見つかりませんでした。\nすみませんが、「質問」からやり直してください",
                "quickReply" => [
                    "texts" => NULL
                ]
            ];
            return $message;
        } else { // あいまい、["プログラミング実験 [AG]","プログラミング実験 [BT]","プログラミング実験 [AM]","コンピュータサイエンス応用実験I [A]","コンピュータサイエンス応用実験I [B]","コンピュータサイエンス応用実験I [C]","プログラミング実験 [BF]"]
            if ($count > 1) { // 複数
                $dialog_node = 'node_1_1606031433273';
                // 会話dbに保存
                DB::table('conversations')->where('userid', $userId)
                    ->update([
                        'conversation_id' => $conversation_id,
                        'dialog_node' => $dialog_node,
                    ]);
                // メッセージ生成
                $names = [];
                $infos = DB::table('reference_informations')->distinct()
                    ->where('lecture_name', 'LIKE', $text)->get();
                foreach ($infos as $info) {
                    $names[] = $info->lecture_name;
                }
                // names配列切り取り限度13(line quick reply)
                $names13 = array_slice($names, 0, 13);
                $message = [
                    "to" => [$userId],
                    "type" => "text",
                    "text" => $count . "件見つかりました。\n講義を選択してください。\nクイックリプライになければ入力してください",
                    "quickReply" => [
                        "texts" => $names13
                    ]
                ];
                return $message;
            } else {
                // $textで参考書検索
                $referenceInfomations = DB::table('reference_informations')
                    ->where('lecture_name', 'LIKE', $text)->get();
                error_log(count($referenceInfomations));
                $count = count($referenceInfomations);
                //一つあるだけ["コンピュータサイエンス応用実験Ⅰ[A]"など
                $referenceInfomation = $referenceInfomations->first();
                $dialog_node = 'root';
                // 会話dbに保存
                DB::table('conversations')->where('userid', $userId)
                    ->update([
                        'conversation_id' => $conversation_id,
                        'dialog_node' => $dialog_node,
                    ]);
                error_log($referenceInfomation->reference_name);
                // メッセージ生成
                $message = [
                    "to" => [$userId],
                    "type" => "text",
                    "text" => $referenceInfomation->reference_name,
                    "quickReply" => [
                        "texts" => NULL
                    ]
                ];
                return $message;
            }
        }
    }

    // 参考書サーチ2
    public function serchReference2($userId, $text, $conversation_id, $lecture_name)
    {
        // $textで講師検索
        $referenceInfomations = DB::table('reference_informations')
            ->where([
                ['lecture_name', $lecture_name],
                ['teacher_name', 'LIKE', '%' . $text . '%']
            ])->get();
        $dialog_node = 'root';
        // ない
        if ($referenceInfomations->isEmpty()) {
            // 会話dbに保存
            DB::table('conversations')->where('userid', $userId)
                ->update([
                    'conversation_id' => $conversation_id,
                    'dialog_node' => $dialog_node,
                ]);
            // メッセージ生成
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => "講師が見つかりませんでした。\nすみませんが、「質問」からやり直してください",
                "quickReply" => [
                    "texts" => NULL
                ]
            ];
            return $message;
        } else {
            // 一つある
            $referenceInfomation = $referenceInfomations->first();
            // 会話dbに保存
            DB::table('conversations')->where('userid', $userId)
                ->update([
                    'conversation_id' => $conversation_id,
                    'dialog_node' => $dialog_node,
                ]);
            error_log($referenceInfomation->reference_name);
            // メッセージ生成
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => $referenceInfomation->reference_name,
                "quickReply" => [
                    "texts" => NULL
                ]
            ];
            return $message;
        }
    }
}
