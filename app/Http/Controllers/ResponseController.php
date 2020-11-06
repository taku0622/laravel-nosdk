<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function index()
    {
        return view('index');
    }
    public function response(Request $request)
    {
        error_log("hello......");
        $input = file_get_contents('php://input');
        error_log($input);
        $event = json_decode($input, true);
        echo json_encode($event, JSON_UNESCAPED_UNICODE);
    }
}
