<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index($id)
    {
        error_log("hello...");
        error_log($id);
    }
}
