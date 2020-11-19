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
        // 学部判定
        $numTwoDigits = substr($input["number"], 0, 2);
        switch ($numTwoDigits) {
            case 'B0':
                $department = 'BS';
                break;
            case 'C0':
                $department = 'CS';
                break;
            case 'M0':
                $department = 'MS';
                break;
            case 'E0':
                $department = 'ES';
                break;
            case 'D0':
                $department = 'DS';
                break;
            case 'H0':
                $department = 'HS';
                break;
            case 'H0':
                $department = 'HS';
                break;
            case 'D1':
            case 'D2':
            case 'D3':
                $department = '院八';
                break;
            default:
                $department = '全学部';
                break;
        }
        DB::table('students')->where('user_id', $id)->update(
            [
                "number" => $input["number"],
                "department" => $department,
                "push_new" => $input["push_new"],
                "push_important" => $input["push_important"],
                "push_cancel" => $input["push_cancel"],
                "push_event" => $input["push_event"],
            ]
        );
        $student = DB::table('students')->where('user_id', $id)->first();
        error_log(json_encode($student, JSON_UNESCAPED_UNICODE));
        return "connected!!";
    }
}
