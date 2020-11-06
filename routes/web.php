<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LineBotController;
use App\Http\Controllers\ResponseController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//   return view('welcome');
// });

// Route::post('/api', [ResponseController::class, 'response']);
Route::get('/hello', [LineBotController::class, 'index']);
Route::get('/res', [ResponseController::class, 'index']);
// Route::get('/hello', 'LineBotController@index');
