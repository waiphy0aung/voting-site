<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function Vote (Request $request) {
        try{
            $missId = $request->missId;
            $categoryId = $request->categoryId;
            $find = Vote::where('userId',Auth::id())->where('missId',$missId)->where('categoryId',$categoryId)->first();
            if(!$find){
                Vote::create([
                    'userId' => Auth::id(),
                    'missId' => $missId,
                    'categoryId' => $categoryId
                ]);
            }else{
                Vote::find($find->id)->delete();
            }
            return response()->json(['data' => $find,'status' => 'success'],200);
        }catch(Exception $e){
            return response()->json(['data'=>$e->getMessage(),'status'=>'error'],500);
        }
    }
}
