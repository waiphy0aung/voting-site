<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\LockController;
use App\Http\Controllers\api\MissController;
use App\Http\Controllers\api\VoteController;
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
    Route::get('/users/{user}', 'getUsers')->middleware('auth:api');
    Route::delete('/users/delete/{user}','deleteUser')->middleware('auth:api');
    Route::post('/auth/login', 'Login');
    Route::get('/auth/login', 'LoginWithToken')->middleware('auth:api');
    Route::post('/auth/register', 'Signup');
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('/add-category-data','addCategoryData');
    Route::middleware('auth:api')->group(function () {
        Route::get('/categories', 'categories');
        Route::post('/categories/create', 'create');
        Route::put('/categories/update/{category}', 'update');
        Route::delete('/categories/delete/{category}', 'delete');
    });
});

Route::controller(MissController::class)->group(function () {
    Route::get('/add-misses-data','addMissesData');
    Route::middleware('auth:api')->group(function () {
        Route::get('/misses', 'misses');
        Route::get('/misses/{miss}', 'misses');
        Route::post('/misses/create', 'create');
        Route::post('/misses/update/{miss}', 'update');
        Route::delete('/misses/delete/{miss}', 'delete');
    });
});

Route::controller(VoteController::class)->group(function () {
    Route::post('/vote', 'Vote')->middleware('auth:api');
});

Route::controller(LockController::class)->middleware('auth:api')->group(function(){
    Route::get('/lock','getLock');
    Route::post('/lock','lock');
    Route::post('/lock/voting-time','votingTime');
});
