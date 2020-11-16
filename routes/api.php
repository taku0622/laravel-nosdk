<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LineBotController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DataBaseController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v1/messages/reply', [ResponseController::class, 'response']);

Route::post('/parrot', [LineBotController::class, 'parrot']);

Route::get('/v1/settings/{id?}', [SettingController::class, 'index']);

Route::post('/v1/settings/{id?}', [SettingController::class, 'update']);

// 新着情報の送信：POST base/v1/infos/new
Route::post('/v1/infos/new', [DataBaseController::class, 'updateNew']);
// 休講情報の送信：POST base/v1/infos/lecture
Route::post('/v1/infos/lecture', [DataBaseController::class, 'updateCancel']);
// 参考書情報の送信：POST base/v1/infos/reference
Route::post('/v1/infos/reference', [DataBaseController::class, 'updateReference']);



// Route::get('/v1/settings/{id?}', function () {
//     return 'PHP Framework Laravel Routing!!';
// });
