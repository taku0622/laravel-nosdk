<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index($id)
    {
        error_log("hello...");
        error_log("id: " . $id);
        return "connected!!";
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
        $userId = $id;
        return view('index2', compact('userId'));
        // return "connected user is :" . $id . PHP_EOL;
    }
}
