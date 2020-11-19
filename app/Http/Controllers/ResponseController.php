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
                    $Response = $watson->watson($userId, $text);
                    $message = [
                        "to" => [$userId],
                        "type" => "text",
                        "text" => $Response[0],
                        "quickReply" => [
                            "texts" => $Response[1]
                        ]
                    ];
                    break;
            }
            $message = array($message);
            error_log(json_encode($message, JSON_UNESCAPED_UNICODE));
            error_log(gettype(json_encode($message, JSON_UNESCAPED_UNICODE)));
            return json_encode($message, JSON_UNESCAPED_UNICODE);
            // echo json_encode($message, JSON_UNESCAPED_UNICODE);
        }
    }

    public function newInfo($userId, $text): array
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

    public function importantInfo($userId, $text): array
    {
        $contents = [
            [
                'title' => '【2020年度後期　履修に関する掲示一覧(八王子キャンパス)',
                'content' => '履修に関する掲示一覧(八王子キャンパス)',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/2018/',
                'label' => '詳細'
            ],
            [
                'title' => '2020年度後期 遠隔システム（Zoom）による相談受け付けについて',
                'content' => '前期に引き続き後期も遠隔システム（Zoom）で相談を受け付けます。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/93230/',
                'label' => '詳細'
            ],
            [
                'title' => '【八王子みなみ野駅】行きスクールバス発着所',
                'content' => '【八王子みなみ野駅】行きスクールバス発着所を一時変更いたしますので、ご確認ください。',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/95242/',
                'label' => '詳細'
            ],
            [
                'title' => 'オンライン大学祭の開催について',
                'content' => '今年度は新型コロナウイルスの影響',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/95052/',
                'label' => '詳細'
            ],
            [
                'title' => '【追加募集】令和2年度日本学生支援',
                'content' => '日本学生支援機構から給付型奨学金及び第二種奨学金',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/95044/',
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

    public function cancelInfo($userId, $text): array
    {
        date_default_timezone_set('Asia/Tokyo');
        $today = date("Y-m-d");
        error_log($today);
        $student = DB::table('students')->where('user_id', $userId)->first();
        error_log($student->department);
        if ($student->department == "全学部") {
            $cancelInfomations = DB::table('cancel_informations')
                ->orderBy('date', 'asc')->get();
        } else {
            $cancelInfomations = DB::table('cancel_informations')
                ->where([
                    ['department', $student->department],
                    ['date', '<=', $today]
                ])
                ->orderBy('date', 'asc')->limit(5)->get();
        }
        if ($cancelInfomations->isEmpty()) {
            $message = [
                "to" => [$userId],
                "type" => "text",
                "text" => $student->department . "の休講案内はありません",
            ];
        } else {
            $contents = [];
            foreach ($cancelInfomations as $cancelInfomation) {
                $content = [
                    'title' => $cancelInfomation->date . "\n"  .
                        $cancelInfomation->period . "\n" .
                        $cancelInfomation->lecture_name,
                    'content' => $cancelInfomation->department,
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

    public function eventInfo($userId, $text): array
    {
        $contents = [
            [
                'title' => '『学内ミニ合同企業説明会』の開催について',
                'content' => '【対象:2020年3月卒業･修了予定者】',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/76553/',
                'label' => '詳細'
            ],
            [
                'title' => 'クリエイティブ業界学内合同企業セミナーのお知らせ',
                'content' => '学内合同企業セミナーを実施します',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/35103/',
                'label' => '詳細'
            ],
            [
                'title' => '大林組グループ会社説明会』の開催について',
                'content' => '対象:学部3年生および大学院修士1年生／学部不問】',
                'uri' => 'https://service.cloud.teu.ac.jp/inside2/archives/53517/',
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

    public function followEvent($userId)
    {
        $student = DB::table('students')->where('user_id', $userId)->first();
        error_log(json_encode($student));
        error_log(isset($student->user_id));
        if (!isset($student->user_id)) {
            DB::table('students')->insert(
                [
                    'user_id' => $userId,
                    'number' => "",
                    'department' => "全学部",
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
}
