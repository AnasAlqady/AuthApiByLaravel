<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\JWTAuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    die(1);
});
Route::group(['middleware'=>'api','prefix'=>'auth'],function($router){
    Route::post('/register', [JWTAuthController::class, 'register']);
    Route::post('/login', [JWTAuthController::class, 'login']);
    Route::get('/logout', [JWTAuthController::class, 'logout']);
});

  
