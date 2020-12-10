<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Actives\Actives;

class SettingController extends Controller
{
    public function index($id) // 設定画面の表示
    {
        // 分析
        $active = new Actives();
        $active->Actives([$id], '設定');
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
            case 'b0':
                $department = 'bs';
                break;
            case 'C0':
            case 'c0':
                $department = 'cs';
                break;
            case 'M0':
            case 'm0':
                $department = 'ms';
                break;
            case 'E0':
            case 'e0':
                $department = 'es';
                break;
            case 'D0':
            case 'd0':
                $department = 'ds';
                break;
            case 'H0':
            case 'h0':
                $department = 'hs';
                break;
            case 'M1':
            case 'm1':
            case 'M2':
            case 'm2':
            case 'M3':
            case 'm3':
            case 'D1':
            case 'd1':
            case 'D2':
            case 'd2':
            case 'D3':
            case 'd3':
                $department = 'inhachi';
                break;
            default:
                $department = 'all_department';
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
