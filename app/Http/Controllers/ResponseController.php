<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function response(Request $request)
    {
        error_log("hello......");
        error_log('1' . gettype($request));
        error_log('2' . json_encode($request, JSON_UNESCAPED_UNICODE));
        error_log('3' . $request->getContent());
    }
}
