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
        // error_log(gettype(json_encode($profile)));
        error_log(json_encode($profile));
        return json_encode($profile);
    }

    public function update($id, Request $request) // 設定の更新
    {
        error_log("設定の更新");
        error_log("id :" . $id);
        $input = json_decode($request->getContent(), true);
        // $student = DB::table('students')->where('user_id', $id)->first();
        error_log("number :" . $input["number"]);
        // {"number":"C0117253","push_new":true,"push_important":true,"push_cancel":true,"push_event":false}
        return "connected!!";
    }
}
