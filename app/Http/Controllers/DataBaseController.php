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
        $input = $request->getContent();
        error_log("input: " . $input);
        // 既にデータがあるか
        // ない場合
        return "connected!! updateCancel";
        // return "connected request is :" . json_encode($input, JSON_UNESCAPED_UNICODE) . PHP_EOL;
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
}
