<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Watson;

class ResponseController extends Controller
{
    public function response(Request $request)
    {
        $events = json_decode($request->getContent(), true);
        foreach ($events as $event) {
            error_log("################################## event is ##################################");
            // error_log(json_encode($event, JSON_UNESCAPED_UNICODE));
            // $usersId = $event["to"]; // array
            $userId = $event["to"][0]; // string
            $type = $event["type"];
            $text = $event["text"];
            error_log("userId: " . $userId . "  type: " . $type . "  text: " . $text);

            // foreach ($usersId as $userId) {
            //     error_log("################################## user is ##################################");
            //     error_log("userId: " . implode(",", $userId) . "  type: " . $type . "  text: " . $text);
            // }

            // 送信のデータの作成
            switch ($text) {
                case "新着情報":
                    $message = $this->newInfo($userId, $text);
                    break;
                case "重要情報":
                    $message = importantInfo($userId, $text);
                    break;
                case "休講案内":
                    $message = cancelInfo($userId, $text);
                    break;
                case "イベント":
                    $message = eventInfo($userId, $text);
                    break;
                default:
                    $message = watson($userId, $text);
                    break;
            }

            return error_log(json_encode($message, JSON_UNESCAPED_UNICODE));
        }
    }

    public function new_info($userId, $text): array
    {
        $contents = [
            [
                'title' => '【図書館】リクエストの結果報告＜八王子キャンパス＞',
                'content' => '10月（前半）の選書の結果、以下のリクエストが採択されました。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/64555/',
                'label' => '詳細'
            ],
            [
                'title' => '2020年度第2学期（後期）放送大学特別聴講学生',
                'content' => '放送大学特別聴講学生へ',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/wp-content/uploads/2020/10/2020_dai2gakki_housoudaigaku_1022.pdf',
                'label' => '詳細'
            ],
            [
                'title' => '【CS学部】2020年度「創成課題」教室（10/22更新）',
                'content' => '属された研究室ごとに、創成課題を行います。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/wp-content/uploads/2020/10/2020CS_souseikadai_kyousitu20201022.pdf',
                'label' => '詳細'
            ],
            [
                'title' => 'シェアサイクル設置のお知らせ（八王子キャンパス）',
                'content' => '八王子キャンパスにシェアサイクルを設置することになりました。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/wp-content/uploads/2020/10/shearingu_settiosirase_1021.pdf',
                'label' => '詳細'
            ],
            [
                'title' => '【図書館】図書館アルバイトを募集します！＜八王子キャンパス＞',
                'content' => 'お申し込みを お待ちしています。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/12658/',
                'label' => '詳細'
            ]
        ];
        $message = [
            "to" => [$userId],
            "type" => "multiple",
            "altText" =>  $text,
            "contents" => $contents
        ];
        return $message;
    }
}
