<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\PushInfo\PushInfo;

class DataBaseController extends Controller
{
    public function updateNew(Request $request)
    {
        $inputs = $request->getContent();
        error_log("input: " . $inputs);
        $inputs = json_decode($inputs, true);
        // pushの配列を作る
        $pushImportant = [];
        $pushNew = [];
        foreach ($inputs as $input) {
            error_log("input[date]: " . $input["date"]); // 1
            error_log("input[title]: " . $input["title"]); // 2
            error_log("input[uri]: " . $input["uri"]); // 3
            error_log("input[tags][0]: " . $input["tags"][0]); // 4
            error_log("input[context]: " . $input["context"]); // 5
            $posted_date = mb_substr($input["date"], 0, 10); // 2020年11月29
            $search = ['年', '月']; //置換する文字
            $posted_date = str_replace($search, '-', $posted_date); //置換
            error_log("posted_date: " . $posted_date);
            // データがない場合
            if (DB::table('informations')->where([
                ['title', $input["title"]],
                ['uri', $input["uri"]]
            ])->doesntExist()) {

                //　データを入れる
                DB::table('informations')->insert([
                    'title' => $input["title"],
                    'content' => $input["context"],
                    'uri' => $input["uri"],
                    'posted_date' => $posted_date
                ]);
                // tagsテーブルにデータを入れる
                $insertInformation = [];
                foreach ($input["tags"] as $tag) {
                    switch ($tag) {
                        case '院八':
                            $tag = 'inhachi';
                            break;
                        case '院工学':
                            $tag = 'inkogaku';
                            break;
                        case '院DS':
                            $tag = 'inds';
                            break;
                        case '重要':
                            $tag = 'important';
                            break;
                        case '全学部':
                            $tag = 'all_department';
                            break;
                        default:
                            $tag = mb_strtolower($tag);
                            break;
                    }
                    $insertInformation[$tag] = true;
                }
                // information_idの取得
                $lastData = DB::table('informations')->orderBy('id', 'desc')->first();
                $informationTags = $insertInformation;
                $insertInformation['information_id'] = $lastData->id;
                DB::table('tags')->insert($insertInformation);

                if (array_key_exists("important", $informationTags)) {
                    // push 重要情報 [1,2,3,4]
                    $pushImportant[] = $lastData->id;
                } else { //　[[[all_department, cs, ms, bs], 5] ]
                    $pushNew[] = [array_keys($informationTags), $lastData->id];
                }
            }
        }
        // Pushの処理
        $push = new PushInfo();
        // if ($pushImportant != []) {
        //     error_log(json_encode($pushImportant), JSON_UNESCAPED_UNICODE);
        //     $push->pusnImportant($pushImportant);
        // }
        if ($pushNew != []) {
            error_log(json_encode($pushNew), JSON_UNESCAPED_UNICODE);
            $push->pusnNew($pushNew);
        }
        return "connected!! updateNew";
    }

    public function updateCancel(Request $request)
    {
        $inputs = $request->getContent();
        error_log("input: " . $inputs);
        $inputs = json_decode($inputs, true);
        // pushの配列を作る
        $pushCancel = [];
        foreach ($inputs as $input) {
            error_log("データベースに入れるデータに変換###################################");
            // データ整形
            $date = mb_substr($input["date"], 0, 10); // 2020年11月29
            $period = mb_substr($input["date"], 12); // 3限目
            $search = ['年', '月']; //置換する文字
            $date = str_replace($search, '-', $date); //置換
            $posted_date = mb_substr($input["up"], 0, 10); // 2020年11月29
            $posted_date = str_replace($search, '-', $posted_date); //置換
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
                    $department = 'es';
                    break;
                case 'デザイン学部':
                    $department = 'ds';
                    break;
                default:
                    $department = 'es';
                    break;
            }
            // データがない場合
            if (DB::table('cancel_informations')->where([
                ['lecture_name', $input["title"]],
                ['teacher_name', $input["instructor"]],
            ])->doesntExist()) {
                //　データを入れる
                DB::table('cancel_informations')->insert([
                    'date' => $date,
                    'period' => $period,
                    'lecture_name' => $input["title"],
                    'teacher_name' => $input["instructor"],
                    'grade' => $input["grade"],
                    'department' => $department,
                    'class' => $input["class"],
                    'note' => $input["note"],
                    'posted_date' => $posted_date,
                    'contributor' => $input["from"]
                ]);
                // information_idの取得
                $lastData = DB::table('cancel_informations')->orderBy('id', 'desc')->first();
            }
        }
        return "connected!! updateCancel";
    }
    public function updateReference(Request $request)
    {
        $inputs = $request->getContent();
        error_log("input: " . $inputs);
        $inputs = json_decode($inputs, true);
        foreach ($inputs as $input) {
            DB::table('reference_informations')->updateOrInsert(
                ['lecture_name' => $input["title"], 'teacher_name' => $input["instructor"]],
                ['reference_name' => $input["Reference"]]
            );
        }
        return "connected!! updateReference";
    }
}
