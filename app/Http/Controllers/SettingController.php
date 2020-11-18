<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index($id)
    {
        error_log("hello...");
        error_log("id: " . $id);
        $profile = [
            "id" => $id,
            "push_new" => true,
            "push_important" => true,
            "push_cancel" => false,
            "push_event" => true,
        ];
        // return "connected!!";
        error_log(json_encode($profile));
        error_log(gettype(json_encode($profile)));
        return json_encode($profile);
        // return "connected user is :" . $id . PHP_EOL;
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

    public function index2($id)
    {
        error_log("hello...");
        error_log("id: " . $id);
        // $userId = $id;
        $userId = 2;
        return view('setting.index2', compact('userId'));
        // return "connected user is :" . $id . PHP_EOL;
    }
}
