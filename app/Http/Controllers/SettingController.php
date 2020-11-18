<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index($id) // 設定画面の表示
    {
        error_log("設定画面の表示");
        // error_log("id: " . $id);
        $student = DB::table('students')->where('user_id', $id)->first();
        // $student = DB::table('students')->first();
        // error_log(json_encode($student, JSON_UNESCAPED_UNICODE));
        // if ($student->isEmpty()) {
        //     error_log("データがありません");
        // } else {
        //     error_log($student->number);
        // }
        $profile = [
            "number" => $student->number,
            "push_new" => $student->push_new,
            "push_important" => $student->push_important,
            "push_cancel" => $student->push_cancel,
            "push_event" => $student->push_event,
        ];
        error_log(gettype(json_encode($profile)));
        return json_encode($profile);
    }

    public function update(Request $request) // 設定の更新
    {
        error_log("設定の更新");
        error_log($request->getContent());
        $input = $request->getContent();
        error_log("input: " . $input);
        return "connected!!";
        // return "connected request is :" . json_encode($input, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
}
