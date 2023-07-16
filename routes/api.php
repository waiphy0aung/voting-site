<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\VoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Auth
Route::controller(AuthController::class)->group(function(){
    Route::get('/users','getUsers')->middleware('auth:api');
    Route::post('/login','Login');
    Route::post('/signup','Signup');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
 });

Route::controller(VoteController::class)->group(function(){
    Route::middleware('auth:api')->group(function(){
        Route::post('/competitors','competitors');
        Route::get('/vote-competitors','voteCompetitors');
        Route::post('/competitor/create','create');
        Route::post('/competitor/{competitor}/update','update');
        Route::delete('/competitor/{competitor}/delete','delete');
        Route::post('/competitor/vote','voteCompetitor');
    });
});
