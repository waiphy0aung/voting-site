<?php

namespace App\Http\Controllers\api;

use Session;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function getUsers(){
        $user = User::all();
        $arr = [];
        foreach($user as $key => $u){
            $id = $user[$key]->id;
            $voter_id = $user[$key]->voter_id;
            $password = $user[$key]->password;
            $url = ['id' => $id,'voter_id' => $voter_id,'password' => $password];
            array_push($arr,$url);
        }
        return $arr;
    }

    public function Login(Request $request){
        $user = User::where('voter_id',$request->voter_id)->where('password',$request->password)->first();
        if($user){
            $credentials = [
                'voter_id' => $request->voter_id,
                'password' => $user->slug,
            ];
        }else{
            return response()->json(['data' => 'unauthorized bitch!','status' => 500 , 'success' => false]);

        }

        if (Hash::check($user->slug, $request->password)) {
            $authUser = $user;
            $token = $authUser->createToken($authUser->slug . '_' . now());
            $auth = json_encode(['data' => ['token' => $token->accessToken,'user'=> $authUser],'status' => 200,'success' => true]);
            return "<script>
                localStorage.setItem('auth',JSON.stringify({$auth}))
                localStorage.setItem('role','{$user->role}')
                window.location.href = '/home';
            </script>";

        } else {
            return response()->json(['data' => 'unauthorized','status' => 500 , 'success' => false]);
        }

    }

    public function register(Request $request){
        $user = User::create([
            'voter_id' => $request->voter_id,
            'role' => 'user',
            'password' => $request->password,
            'number_of_vote' => $request->no
        ]);
    }



}

