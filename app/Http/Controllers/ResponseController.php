<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function index()
    {
        return view('index');
    }
    public function response()
    {
        $input = file_get_contents('php://input');
        error_log($input);
    }
}
