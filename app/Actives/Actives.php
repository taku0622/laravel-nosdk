<?php

namespace App\Actives;

use Illuminate\Support\Facades\DB;


class Actives
{
    public function Actives($idList, $text)
    {
        error_log("-------------- active mode -----------");
        date_default_timezone_set('Asia/Tokyo');
        error_log(date('Y-m-d H:i:s'));
        // どのカラムにするかの分岐
        switch ($text) {
            case "質問":
                $column = 'question_count';
                break;
            case "重要情報":
                $column = 'important_count';
                break;
            case "新着情報":
                $column = 'new_count';
                break;
            case "休講案内":
                $column = 'canel_count';
                break;
            case "イベント":
                $column = 'event_count';
                break;
            case "設定":
                $column = 'setting_count';
                break;
            case "push_important_count":
                $column = 'push_important_count';
                break;
            case "push_new_count":
                $column = 'push_new_count';
                break;
            case "push_cancel_count":
                $column = 'push_cancel_count';
                break;
            case "push_event_count":
                $column = 'push_event_count';
                break;
            default:
                $column = 'other_count';
                break;
        }
        foreach ($idList as $userId) {
            // データがあるか
            error_log("userId: " . $userId . "  text: " . $text);
            DB::table('actives')->where('user_id', $userId)
                ->increment($column, 1, ['updated_at' => date('Y-m-d H:i:s')]);
        }
    }
}
