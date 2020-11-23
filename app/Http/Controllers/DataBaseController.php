<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\push\PushCancelInfo;
use App\push\PushEventInfo;

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
        $pushCancelInfo = new PushCancelInfo();
        $pushCancelInfo->pushCancelInfo();
        $pushEventInfo = new PushEventInfo();
        $pushEventInfo->pushEventInfo();
        return "connected!! updateCancel";
    }
    public function updateReference(Request $request)
    {
        error_log("hello...");
        error_log($request->getContent());
        $inputs = $request->getContent();
        error_log("input: " . $inputs);
        error_log(gettype($inputs));
        $inputs = json_decode($inputs, true);
        $insertInformations = [];
        foreach ($inputs as $input) {
            error_log("input[name]: " . $input["name"]); // 1
            error_log("input[instructor]: " . $input["ninstructor"]); // 1
            error_log("input[reference]: " . $input["reference"]); // 1
        }
        return "connected!! updateReference";
        // return "connected request is :" . json_encode($input, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
}
