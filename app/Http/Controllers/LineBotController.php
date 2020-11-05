<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineBotController extends Controller
{
    public function index()
    {
        return view('linebot.index');
    }
    public function response(Request $request)
    {
        error_log("hello...");

        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $events = json_decode($input)->events;
            foreach ($events as $event) {
                error_log(json_encode($event, JSON_UNESCAPED_UNICODE));
                // bot($event);
            }
        }
    }
}
