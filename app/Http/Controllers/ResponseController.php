<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Watson;

class ResponseController extends Controller
{
    public function response(Request $request)
    {
        $events = json_decode($request->getContent(), true);
        foreach ($events as $event) {
            error_log("################################## event is ##################################");
            error_log(json_encode($event, JSON_UNESCAPED_UNICODE));
            $usersId = $event["to"]; // array
            $type = $event["type"];
            $text = $event["text"];
            error_log("usersId: " . implode(",", $usersId) . "  type: " . $type . "  text: " . $text);
        }
    }
}
