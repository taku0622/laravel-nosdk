<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DataBaseController extends Controller
{
    public function updateNew(Request $request)
    {
        error_log("hello...");
        error_log($request->getContent());
        $input = $request->getContent();
        error_log("input: " . $input);
        return "connected!! updateNew";
        // return "connected request is :" . json_encode($input, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
    public function updateCancel(Request $request)
    {
        error_log("hello...");
        error_log($request->getContent());
        $inputs = $request->getContent();
        error_log("input: " . $inputs);
        error_log(gettype($inputs));
        $inputs = json_decode($inputs, true);
        $insertInformations = [];
        foreach ($inputs as $input) {
            error_log("input[day]: " . $input["day"]); // 1
            error_log("input[name]: " . $input["name"]); // 2
            error_log("input[instructor]: " . $input["instructor"]); // 3
            error_log("input[department]: " . $input["department"]); // 4
            error_log("input[grade]: " . $input["grade"]); // 5
            error_log("input[class]: " . $input["class"]); // 5
            error_log("input[note]: " . $input["note"]); // 6
            error_log("input[up]: " . $input["up"]); // 7
            error_log("input[from]: " . $input["from"]); // 8
            error_log("データベースに入れるデータに変換###################################");
            // データ整形
            $date = mb_substr($input["day"], 0, 10); // 2020年11月29
            $period = mb_substr($input["day"], 12); // 3限目
            $search = ['年', '月']; //置換する文字
            $date = str_replace($search, '-', $date); //置換
            # 学部
            switch ($input["department"]) {
                case '応用生物学部':
                    $department = 'bs';
                    break;
                case 'コンピュータサイエンス学部':
                    $department = 'cs';
                    break;
                case 'メディア学部':
                    $department = 'ms';
                    break;
                case '工学部':
                case '機械工学科':
                    $department = 'es';
                    break;
                case 'デザイン学部':
                    $department = 'ds';
                    break;
                default:
                    $department = 'es';
                    break;
            }
            $posted_date = mb_substr($input["up"], 0, 10); // 2020年11月29
            $posted_date = str_replace($search, '-', $posted_date); //置換
            error_log("date:" . $date); // 1
            error_log("period:" . $period); // 2
            error_log("lecture_name:" . $input["name"]); // 3
            error_log("teacher_name:" . $input["instructor"]); // 4
            error_log("grade:" . $input["grade"]); // 5
            error_log("department:" . $department); // 6
            error_log("class:" . $input["class"]); // 7
            error_log("note: " . $input["note"]); // 8
            error_log("posted_date: " . $posted_date); // 9
            error_log("contributor: " . $input["from"]); // 10
            // 0:00に定期実行と定義する
            // 昨日
            date_default_timezone_set('Asia/Tokyo');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $insertInformation = [
                'date' => $date,
                'period' => $period,
                'lecture_name' => $input["name"],
                'teacher_name' => $input["instructor"],
                'grade' => $input["grade"],
                'department' => $department,
                'class' => $input["class"],
                'note' => $input["note"],
                'posted_date' => $posted_date,
                'contributor' => $input["from"],
            ];

            error_log($yesterday);
            if ($posted_date == $yesterday) {
                $insertInformations[] = $insertInformation;
            }
        }
        if ($insertInformations == []) {
            // インサートするデータがあるとき
            error_log("no list");
        } else {
            // インサートするデータがないとき
            error_log("exist");
            DB::table('cancel_informations')->insert($insertInformations);
        }
        $this->postCancelInfo();
        return "connected!! updateCancel";
    }
    public function updateReference(Request $request)
    {
        error_log("hello...");
        error_log($request->getContent());
        error_log(gettype($request->getContent()));
        // foreach ($inputs as $input) {
        //     // データ整形
        //     $input = json_decode($input, true);
        //     error_log("input[day]: " . $input["day"]);
        // }

        return "connected!! updateReference";
        // return "connected request is :" . json_encode($input, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
    public function postCancelInfo()
    {
        error_log("pushCancelINfo...");
        // push通知オンの人を集める
        $students = DB::table('students')->select('user_id')
            ->where('push_cancel', true)->first();
        error_log(json_encode($students, JSON_UNESCAPED_UNICODE));
        $userId = "U6e0f4008a090ff5b5bef0323cae3428e";
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
            "altText" =>  "休講案内",
            "contents" => $contents
        ];
        $data = json_encode([$message], JSON_UNESCAPED_UNICODE);
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
