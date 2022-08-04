<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VoterController extends Controller
{
    public function index(){
        $competitors = Competitor::all();
        return view('welcome',['competitors'=>$competitors]);
    }

    public function login(Request $request){
        $voter = Voter::where('voter_id',$request->voter_id)->first();
        if(!$voter){
            return redirect()->route('welcome');
        }else{
            if($request->password === $voter->password){
                $competitors = Competitor::all();
                return response()->json(['data' => '']);
            }else{
                return redirect()->route('welcome');
            }
        }
    }

    public function competitors(){
        return Competitor::all();
    }

    public function kings(){
        return Competitor::where('role','king')->get();
    }

    public function queens(){
        return Competitor::where('role','queen')->get();
    }

}
