<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Watson;

class ResponseController extends Controller
{
    public function response(Request $request)
    {
        error_log("hello......");
        // error_log('1' . gettype($request));
        // error_log('2' . json_encode($request, JSON_UNESCAPED_UNICODE));
        error_log('3' . $request->getContent());
        $events = $request->getContent();
        error_log('4' . $events[0]);
        // error_log('3' . $request->getContent());
    }
}
