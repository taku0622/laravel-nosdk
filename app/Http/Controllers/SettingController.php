<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index($id)
    {
        error_log("hello...");
        error_log("id: " . $id);
        #################################################################
        $student = DB::table('students')->where('user_id', $id)->get();
        // if ($cancelInfomations->isEmpty()) {
        //     $message = "あなたの学部の休講案内はありません";
        // } else {
        //     $message = "";
        // }
        error_log($student->number);
        #################################################################
        $profile = [
            "number" => $student->number,
            "push_new" => $student->push_new,
            "push_important" => $student->push_important,
            "push_cancel" => $student->push_cancel,
            "push_event" => $student->push_event,
        ];
        error_log(json_encode($profile));
        error_log(gettype(json_encode($profile)));
        return json_encode($profile);
    }

    public function update(Request $request)
    {
        error_log("hello...");
        error_log($request->getContent());
        $input = $request->getContent();
        error_log("input: " . $input);
        return "connected!!";
        // return "connected request is :" . json_encode($input, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
}
