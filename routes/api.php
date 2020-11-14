<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LineBotController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\SettingController;

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

// https://tut-php-api.herokuapp.com/api
// Route::post('/', [ResponseController::class, 'response']);
Route::post('/v1/messages/reply', [ResponseController::class, 'response']);
// https://tut-php-api.herokuapp.com/api/v1/messages/reply
Route::post('/v4/m/reply', [ResponseController::class, 'res']);
// Route::get('/v1/settings/:id?type=js', [ResponseController::class, 'response']);
Route::post('/parrot', [LineBotController::class, 'parrot']);


Route::get('/v1/settings/{id?}', [SettingController::class, 'index']);
// Route::get('/v1/settings/{id?}', function () {
//     return 'PHP Framework Laravel Routing!!';
// });
