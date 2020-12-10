<?php

namespace App\Actives;

use Illuminate\Support\Facades\DB;


class Actives
{
  public function responseActives($userId, $text)
  {
    error_log("-------------- active mode -----------");
    error_log("userId: " . $userId . "  text: " . $text);
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
      default:
        $column = 'other_count';
        break;
    }
    // データがあるか
    if (DB::table('actives')->where('user_id', $userId)->exists()) {
      DB::table('actives')->where('user_id', $userId)
        ->increment($column, 1, ['updated_at' => date('Y-m-d H:i:s')]);
    } else { //ない時
      DB::table('actives')->insert(
        [
          'user_id' => $userId,
          'question_count' => 0,
          'important_count' => 0,
          'new_count' => 0,
          'canel_count' => 0,
          'event_count' => 0,
          'setting_count' => 0,
          'other_count' => 0,
          'push_important_count' => 0,
          'push_new_count' => 0,
          'push_cancel_count' => 0,
          'push_event_count' => 0,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
        ]
      );
      DB::table('actives')->where('user_id', $userId)->increment($column);
    }
  }
  public function pushActives($idList, $column)
  {
    error_log("-------------- active mode -----------");
    date_default_timezone_set('Asia/Tokyo');
    error_log(date('Y-m-d H:i:s'));
    foreach ($idList as $userId) {
      // データがあるか
      if (DB::table('actives')->where('user_id', $userId)->exists()) {
        DB::table('actives')->where('user_id', $userId)
          ->increment($column, 1, ['updated_at' => date('Y-m-d H:i:s')]);
      } else { //ない時
        DB::table('actives')->insert(
          [
            'user_id' => $userId,
            'question_count' => 0,
            'important_count' => 0,
            'new_count' => 0,
            'canel_count' => 0,
            'event_count' => 0,
            'setting_count' => 0,
            'other_count' => 0,
            'push_important_count' => 0,
            'push_new_count ' => 0,
            'push_cancel_count' => 0,
            'push_event_count' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]
        );
        DB::table('actives')->where('user_id', $userId)->increment($column);
      }
    }
  }
}
