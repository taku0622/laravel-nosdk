<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingController;


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

Route::get('/', function () {
  return view('welcome');
});

Route::get('/v2/settings/{id?}', [SettingController::class, 'index2']);

# response接続 
// Route::post('/api/v1/messages/reply', [ResponseController::class, 'response']);
