<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        error_log("hello...");
        error_log($request->getContent());
        $input = $request->getContent();
        error_log("input: " . $input);
    }
}
