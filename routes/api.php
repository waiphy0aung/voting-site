<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\VoteController;
use App\Models\Lock;
use App\Models\User;
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
    Route::get('/login','Login');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
 });

Route::post('/users/create',function(Request $request){
    try{
        User::factory($request->no)->create();
        return response()->json(['data' => 'created successfully','success' => true,'status' => 200]);
    }catch(Throwable $e){
        return response()->json(['data' => $e,'success' => false,'status' => 500]);
    }
})->middleware('auth:api');

Route::post('/users/delete',function(Request $request){
    try{
        $ids = json_decode($request->ids);
        // return $request->ids;
        foreach($ids as $id){
            User::where('id',$id)->delete();
        };
        // User::factory($request->no)->create();
        return response()->json(['data' => 'created successfully','success' => true,'status' => 200]);
    }catch(Throwable $e){
        return response()->json(['data' => $e,'success' => false,'status' => 500]);
    }
})->middleware('auth:api');

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

Route::controller(RoleController::class)->middleware('auth:api')->group(function(){
    Route::get('/roles','getRoles');
    Route::post('/roles/create','create');
    Route::post('/roles/{role}/update','update');
    Route::delete('/roles/{role}/delete','delete');
});

Route::get('/isLock',function(){
    $isLock = Lock::first()->isLock;
    if($isLock == 0){
        return response()->json(['data' => false]);
    }else{
        return response()->json(['data' => true]);
    }
});

Route::post('/setLock',function(){
    $lock = Lock::first();
    if($lock->isLock == 0){
        $lock->update([
            'isLock' => 1
        ]);
    }else{
        $lock->update([
            'isLock' => 0
        ]);
    }
    return response()->json(['success' => true]);
})->middleware('auth:api');
