<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\VoteController;
use App\Http\Controllers\api\CategoryController;
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

Route::get('/unauthorize', function () {
    return response()->json(['data' => 'Unauthenticate', 'status' => 'error']);
})->name('unauthorize');

//Auth
Route::controller(AuthController::class)->group(function () {
    Route::get('/users', 'getUsers')->middleware('auth:api');
    Route::post('/login', 'Login');
    Route::post('/signup', 'Signup');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'categories');
    Route::middleware('auth:api')->group(function () {
        Route::post('/categories/create', 'create');
        Route::post('/categories/{category}/update', 'update');
        Route::delete('/categories/{category}/delete', 'delete');
    });
});

Route::controller(VoteController::class)->group(function () {

    Route::middleware('auth:api')->group(function () {
        Route::get('/competitors', 'competitors');
        Route::get('/competitor/{competitor}', 'competitor');
        Route::get('/vote-competitors', 'voteCompetitors');
        Route::post('/competitor/create', 'create');
        Route::post('/competitor/{competitor}/update', 'update');
        Route::delete('/competitor/{competitor}/delete', 'delete');
        Route::post('/competitor/vote', 'voteCompetitor');
    });
});
